<?php

namespace App\Imports;

use App\Models\WorkingOrder;
use App\Models\WoTrackingLog;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;

class WorkingOrderImport implements ToCollection
{
    protected $region;

    public function __construct($region)
    {
        $this->region = $region;
    }

    public function collection(Collection $rows)
    {
        // Lewati header (baris pertama)
        $rows->splice(0, 1); 

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                // Lewati baris jika isinya benar-benar kosong
                if (!isset($row[0]) && !isset($row[1])) continue;

             
                if ($this->region === 'sumbar') {
                    $wonum        = trim($row[8] ?? $row[9]); // Kolom I atau J (Wonum 1/2)
                    $scid         = trim($row[4] ?? '');      // Kolom E
                    $namaCust     = trim($row[11] ?? '');     // Kolom L
                    $noHp         = trim($row[16] ?? '');     // Kolom Q
                    $alamat       = trim($row[13] ?? '');     // Kolom N
                    $stoRaw       = trim($row[6] ?? '');      // Kolom G
                    $jenisOrder   = trim($row[28] ?? '');     // Kolom AC
                    $paket        = trim($row[14] ?? '');     // Kolom O
                    $statusWoRaw  = trim($row[32] ?? '');     // Kolom AG
                    $detailKendala = trim($row[33] ?? '');    // Kolom AH
                } 
                elseif ($this->region === 'batam') {
                    $wonum        = trim($row[4] ?? '');      // Kolom E
                    $scid         = trim($row[4] ?? '');      // Kolom E (Batam menggabung WONUM & SCID)
                    $namaCust     = trim($row[11] ?? '');     // Kolom L
                    $noHp         = trim($row[13] ?? '');     // Kolom N
                    $alamat       = trim($row[12] ?? '');     // Kolom M
                    $stoRaw       = trim($row[42] ?? '');     // Kolom AQ (SEKTOR)
                    $jenisOrder   = trim($row[6] ?? '');      // Kolom G
                    $paket        = null;                     // (Sesuai catatan mentor: Kosong)
                    $statusWoRaw  = trim($row[25] ?? '');     // Kolom Z (STATUS 1)
                    $detailKendala = trim($row[28] ?? '');    // Kolom AC
                }
                elseif ($this->region === 'ridar') {
                    // Ridar perlu turun 1 baris lagi karena headernya bertumpuk 2 baris
                    if ($index === 0) continue; 
                    
                    $wonum        = trim($row[10] ?? '');     // Kolom K
                    $scid         = trim($row[11] ?? '');     // Kolom L
                    $namaCust     = trim($row[12] ?? '');     // Kolom M
                    $noHp         = trim($row[13] ?? '');     // Kolom N
                    $alamat       = trim($row[14] ?? '');     // Kolom O
                    $stoRaw       = trim($row[15] ?? '');     // Kolom P
                    $jenisOrder   = trim($row[7] ?? '');      // Kolom H
                    $paket        = trim($row[8] ?? '');      // Kolom I
                    $statusWoRaw  = trim($row[21] ?? '');     // Kolom V
                    $detailKendala = trim($row[23] ?? '');    // Kolom X
                }

                // Lewati jika WONUM tidak ada (data tidak valid)
                if (empty($wonum)) continue;

                $masterSto = \App\Models\MasterSto::where('kode_sto', 'LIKE', "%{$stoRaw}%")->first();
                $stoId = $masterSto ? $masterSto->id : null;

                $masterStatus = \App\Models\MasterStatusWo::where('nama_status', $statusWoRaw)->first();
                $statusWoId = $masterStatus ? $masterStatus->id : null;

                // ==========================================
                // 3. UPSERT KE DATABASE (Semua disatukan di sini)
                // ==========================================
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