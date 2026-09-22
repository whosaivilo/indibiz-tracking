<?php
namespace App\Console\Commands;

use App\Models\WorkingOrder;
use App\Models\WoTrackingLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportWoCsv extends Command
{
    // Nama perintah yang akan diketik di terminal
    protected $signature   = 'import:wo-csv';
    protected $description = 'Import dan Upsert data WO dari CSV Indibiz';

    public function handle()
    {
        $this->info('Memulai proses import dari Logbook CSV...');

        // 1. Ubah nama file ke CSV yang baru
        $filePath = storage_path('app/LOGBOOK PROVISIONING RIDAR - PBB.csv');

        if (! file_exists($filePath)) {
            $this->error('File CSV tidak ditemukan!');
            return;
        }

        $file = fopen($filePath, 'r');

        // 2. Lompat dua baris karena baris 1 itu header merged (BY HD, dll)
        // dan baris 2 itu nama kolom aslinya
        fgetcsv($file);
        fgetcsv($file);

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($file)) !== false) {

                                                          // 3. MAPPING INDEX BARU (Sesuai dengan CSV Logbook yang baru)
                $wonum       = trim($row[10]);            // Kolom K (WONUM)
                $segment     = strtoupper(trim($row[9])); // Kolom J (SEGMENT)
                $statusWoRaw = trim($row[21]);            // Kolom V (STATUS WO)

                // Filter INDIHOME dan WONUM Kosong
                if (str_contains($segment, 'INDIHOME') || empty($wonum)) {
                    continue;
                }

                                             // Mapping STO
                $stoRaw    = trim($row[15]); // Kolom P (STO)
                $masterSto = \App\Models\MasterSto::where('kode_sto', 'LIKE', "%{$stoRaw}%")->first();
                $stoId     = $masterSto ? $masterSto->id : null;

                // Mapping Status WO
                $masterStatus = \App\Models\MasterStatusWo::where('nama_status', $statusWoRaw)->first();
                $statusWoId   = $masterStatus ? $masterStatus->id : null;

                // Proses Upsert Working Order
                $workingOrder = WorkingOrder::updateOrCreate(
                    ['wonum' => $wonum],
                    [
                        'scid'                 => trim($row[11]), // Kolom L (SCID)
                        'nama_cust'            => trim($row[12]), // Kolom M (NAMA CUST)
                        'no_hp'                => trim($row[13]), // Kolom N (NO HP)
                        'alamat'               => trim($row[14]), // Kolom O (ALAMAT)
                        'jenis_order'          => trim($row[7]),  // Kolom H (JENIS ORDER)
                        'paket'                => trim($row[8]),  // Kolom I (PAKET)
                        'sto_raw'              => $stoRaw,
                        'sto_id'               => $stoId,
                        'current_status_wo_id' => $statusWoId,
                    ]
                );

                // Proses Insert ke Tracking Log
                $lastLog = WoTrackingLog::where('working_order_id', $workingOrder->id)
                    ->latest('created_at')
                    ->first();

                // Kolom X (DETAIL KENDALA JARINGAN) ada di index 23
                $detailKendala = trim($row[23]);

                if (! $lastLog || $lastLog->status_wo_raw !== $statusWoRaw || $lastLog->catatan !== $detailKendala) {
                    WoTrackingLog::create([
                        'working_order_id' => $workingOrder->id,
                        'status_wo_raw'    => $statusWoRaw,
                        'status_wo_id'     => $statusWoId,
                        'catatan'          => $detailKendala,
                    ]);
                }
            }
            DB::commit();
            $this->info('Import CSV Selesai! Data Logbook berhasil masuk ke database.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }

        fclose($file);
    }
}
