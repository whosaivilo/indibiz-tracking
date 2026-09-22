<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterStatusWoSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            // Status Umum / Irisan
            ['nama_status' => 'COMPLETED', 'is_final' => 1],
            ['nama_status' => 'FO UIM', 'is_final' => 0],
            ['nama_status' => 'CANCEL', 'is_final' => 1],
            ['nama_status' => 'OPEN', 'is_final' => 0],

            // Status by Helpdesk
            ['nama_status' => 'SEND SURVEY', 'is_final' => 0],
            ['nama_status' => 'FAILWA', 'is_final' => 0],
            ['nama_status' => 'PI', 'is_final' => 0],
            ['nama_status' => 'ASSIGN', 'is_final' => 0],
            ['nama_status' => 'PROSES IT TOOLS', 'is_final' => 0],
            ['nama_status' => 'WAPPR', 'is_final' => 0],

            // Status by Korlap / HD
            ['nama_status' => 'Pending > 3', 'is_final' => 0],
            ['nama_status' => 'Material | NTE Kosong', 'is_final' => 0],
            ['nama_status' => 'FO ACT', 'is_final' => 0],
            ['nama_status' => 'PONR', 'is_final' => 0],
            ['nama_status' => 'TARIK DC', 'is_final' => 1], // Asumsi tarik kembali/batal
            ['nama_status' => 'BATAL | APS', 'is_final' => 1],
            ['nama_status' => 'REVOKE TO FOLLOW UP', 'is_final' => 1],
            ['nama_status' => 'KENDALA IKG/IKR', 'is_final' => 0],
            ['nama_status' => 'BATAL | SALAH TAGGING/ TIDAK ADA HOMEPASS', 'is_final' => 1],
            ['nama_status' => 'BATAL | DOUBLE INPUT', 'is_final' => 1],
            ['nama_status' => 'BATAL | RESELLER', 'is_final' => 1],
            ['nama_status' => 'BATAL | EXISTING', 'is_final' => 1],
            ['nama_status' => 'PELANGGAN RAGU', 'is_final' => 0],
            ['nama_status' => 'CHANGE PAKET', 'is_final' => 0],
            ['nama_status' => 'KENDALA INPUTAN', 'is_final' => 0],
            ['nama_status' => 'IKR OK PENDING PLG', 'is_final' => 0],
            ['nama_status' => 'ALAMAT TIDAK KETEMU / CP RNA', 'is_final' => 0],
            ['nama_status' => 'CHANGE SEGMENT', 'is_final' => 0],
            ['nama_status' => 'CUACA', 'is_final' => 0],
            ['nama_status' => 'KENDALA IZIN', 'is_final' => 0],
            ['nama_status' => 'ODP FULL | OX EXPAND', 'is_final' => 0],
            ['nama_status' => 'ODP LOSS/UNSPEK', 'is_final' => 0],
            ['nama_status' => 'ODP BELUM GOLIVE', 'is_final' => 0],
            ['nama_status' => 'BUTUH TANAM TIANG', 'is_final' => 0],
            ['nama_status' => 'ODP JAUH > 250', 'is_final' => 0],
            ['nama_status' => 'CROSSING JALAN', 'is_final' => 0],
            ['nama_status' => 'TIDAK ADA ALPRO', 'is_final' => 0],
            ['nama_status' => 'SPBT TIDAK TEMBUS', 'is_final' => 0],
            ['nama_status' => 'ODP FULL | NOK EXPAND', 'is_final' => 0],
            ['nama_status' => 'ODP FULL + TATI', 'is_final' => 0],
            ['nama_status' => 'ODP BUTUH BENJAR', 'is_final' => 0],
            ['nama_status' => 'ODP BUTUH PS 1:8', 'is_final' => 0],
            ['nama_status' => 'BUTUH GANTI TIANG', 'is_final' => 0],
            ['nama_status' => 'SELESAI PENARIKAN', 'is_final' => 0],
            ['nama_status' => 'SURVEY', 'is_final' => 0],
            ['nama_status' => 'PENDING H+1', 'is_final' => 0],
            ['nama_status' => 'PENDING H+2', 'is_final' => 0],
            ['nama_status' => 'REMANJA JAM VISIT', 'is_final' => 0],
            ['nama_status' => 'PS SC LAIN', 'is_final' => 1],
            ['nama_status' => 'AKTIVASI', 'is_final' => 0],
        ];

        DB::table('master_status_wos')->insert($statuses);
    }
}
