<?php

namespace App\Imports;

use App\Models\WorkingOrder;
use App\Models\WoTrackingLog;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\DB;
use App\Models\MasterSto;
use App\Models\MasterStatusWo;

class WorkingOrderImport implements ToCollection, WithChunkReading, WithStartRow
{
    protected $region;
    protected $stoCache = [];
    protected $statusCache = [];

    public function __construct($region)
    {
        $this->region = $region;
    }

    public function startRow(): int
    {
        // Lewati header. Ridar perlu turun 1 baris lagi karena headernya bertumpuk 2 baris.
        return $this->region === 'ridar' ? 3 : 2;
    }

    public function chunkSize(): int
    {
        return 1000; // Baca 1000 baris sekaligus per proses agar RAM stabil & proses cepat
    }

    public function collection(Collection $rows): void
    {
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                // Lewati baris jika isinya benar-benar kosong
                if (!isset($row[0]) && !isset($row[1])) continue;

                if ($this->region === 'sumbar') {
                    $wonum         = trim($row[8] ?? $row[9] ?? ''); // Kolom I atau J (Wonum 1/2) - Fixed Undefined array key 9
                    $scid          = trim($row[4] ?? '');      // Kolom E
                    $namaCust      = trim($row[11] ?? '');     // Kolom L
                    $noHp          = trim($row[16] ?? '');     // Kolom Q
                    $alamat        = trim($row[13] ?? '');     // Kolom N
                    $stoRaw        = trim($row[6] ?? '');      // Kolom G
                    $jenisOrder    = trim($row[28] ?? '');     // Kolom AC
                    $paket         = trim($row[14] ?? '');     // Kolom O
                    $statusWoRaw   = trim($row[32] ?? '');     // Kolom AG
                    $detailKendala = trim($row[33] ?? '');     // Kolom AH
                } elseif ($this->region === 'batam') {
                    $wonum         = trim($row[4] ?? '');      // Kolom E
                    $scid          = trim($row[4] ?? '');      // Kolom E (Batam menggabung WONUM & SCID)
                    $namaCust      = trim($row[11] ?? '');     // Kolom L
                    $noHp          = trim($row[13] ?? '');     // Kolom N
                    $alamat        = trim($row[12] ?? '');     // Kolom M
                    $stoRaw        = trim($row[42] ?? '');     // Kolom AQ (SEKTOR)
                    $jenisOrder    = trim($row[6] ?? '');      // Kolom G
                    $paket         = null;                     // (Sesuai catatan mentor: Kosong)
                    $statusWoRaw   = trim($row[25] ?? '');     // Kolom Z (STATUS 1)
                    $detailKendala = trim($row[28] ?? '');     // Kolom AC
                } elseif ($this->region === 'ridar') {
                    $wonum         = trim($row[10] ?? '');     // Kolom K
                    $scid          = trim($row[11] ?? '');     // Kolom L
                    $namaCust      = trim($row[12] ?? '');     // Kolom M
                    $noHp          = trim($row[13] ?? '');     // Kolom N
                    $alamat        = trim($row[14] ?? '');     // Kolom O
                    $stoRaw        = trim($row[15] ?? '');     // Kolom P
                    $jenisOrder    = trim($row[7] ?? '');      // Kolom H
                    $paket         = trim($row[8] ?? '');      // Kolom I
                    $statusWoRaw   = trim($row[21] ?? '');     // Kolom V
                    $detailKendala = trim($row[23] ?? '');     // Kolom X
                }

                // Lewati jika WONUM kosong
                if (empty($wonum)) continue;

                // ==========================================================
                // SARINGAN SAMPAH & ANTI CRASH (Mencegah Database Error)
                // ==========================================================
                // 1. Lewati baris jika WONUM diawali rumus Excel "=" atau kata "Achieve"
                if (str_starts_with($wonum, '=') || str_contains(strtolower($wonum), 'achieve')) {
                    continue;
                }

                // 2. Bersihkan glitch rumus Excel di STO (Bug dari file Batam)
                // Mengubah '=IFERROR(...,"SEKTOR BTC")' menjadi murni 'SEKTOR BTC'
                if (str_contains($stoRaw, 'IFERROR')) {
                    $parts = explode(',"', $stoRaw);
                    $stoRaw = str_replace('")', '', end($parts));
                }

                // 3. Amankan batas maksimal karakter HAMPIR SEMUA kolom VARCHAR
                // Ini pelindung mutlak agar database tidak pernah crash (Error 1406) lagi
                $wonum      = substr($wonum, 0, 50);
                $noHp       = substr($noHp, 0, 25);
                $stoRaw     = substr($stoRaw, 0, 50);
                $jenisOrder = substr($jenisOrder, 0, 50);
                $namaCust   = substr($namaCust, 0, 150);
                $paket      = substr($paket, 0, 150);
                // ==========================================================

                // Cache Query STO (Menghindari N+1 Query / Query berulang untuk data yang sama)
                $stoId = null;
                if (!empty($stoRaw)) {
                    if (array_key_exists($stoRaw, $this->stoCache)) {
                        $stoId = $this->stoCache[$stoRaw];
                    } else {
                        $masterSto = MasterSto::where('kode_sto', 'LIKE', "%{$stoRaw}%")->first();
                        $stoId = $masterSto ? $masterSto->id : null;
                        $this->stoCache[$stoRaw] = $stoId;
                    }
                }

                // Cache Query Status WO
                $statusWoId = null;
                if (!empty($statusWoRaw)) {
                    if (array_key_exists($statusWoRaw, $this->statusCache)) {
                        $statusWoId = $this->statusCache[$statusWoRaw];
                    } else {
                        $masterStatus = MasterStatusWo::where('nama_status', $statusWoRaw)->first();
                        $statusWoId = $masterStatus ? $masterStatus->id : null;
                        $this->statusCache[$statusWoRaw] = $statusWoId;
                    }
                }

                $workingOrder = WorkingOrder::updateOrCreate(
                    ['wonum' => $wonum],
                    [
                        'scid'                 => $scid,
                        'nama_cust'            => $namaCust,
                        'no_hp'                => $noHp,
                        'alamat'               => $alamat,
                        'jenis_order'          => $jenisOrder,
                        'paket'                => $paket,
                        'sto_raw'              => $stoRaw,
                        'sto_id'               => $stoId,
                        'current_status_wo_id' => $statusWoId,
                    ]
                );

                $lastLog = WoTrackingLog::where('working_order_id', $workingOrder->id)
                    ->latest('created_at')
                    ->first();

                if (!$lastLog || $lastLog->status_wo_raw !== $statusWoRaw || $lastLog->catatan !== $detailKendala) {
                    WoTrackingLog::create([
                        'working_order_id' => $workingOrder->id,
                        'status_wo_raw'    => $statusWoRaw,
                        'status_wo_id'     => $statusWoId,
                        'catatan'          => $detailKendala,
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}