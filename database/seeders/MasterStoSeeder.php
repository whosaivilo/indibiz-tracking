<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterStoSeeder extends Seeder
{
    public function run(): void
    {
        $stos = [
            // Area Batam
            ['kode_sto' => 'BDS', 'nama_sto' => 'BUKIT DANGAS', 'distrik' => 'BATAM'],
            ['kode_sto' => 'BTC', 'nama_sto' => 'BATAM CENTER', 'distrik' => 'BATAM'],
            ['kode_sto' => 'NGS', 'nama_sto' => 'NONGSA', 'distrik' => 'BATAM'],
            ['kode_sto' => 'PNI', 'nama_sto' => 'PANBIL', 'distrik' => 'BATAM'],
            ['kode_sto' => 'TPI', 'nama_sto' => 'TANJUNG PINANG', 'distrik' => 'BATAM'],

            // Area Bukittinggi
            ['kode_sto' => 'BKT', 'nama_sto' => 'BUKIT TINGGI', 'distrik' => 'BUKITTINGGI'],
            ['kode_sto' => 'BSK', 'nama_sto' => 'BATUSANGKAR', 'distrik' => 'BUKITTINGGI'],
            ['kode_sto' => 'PYK', 'nama_sto' => 'PAYA KUMBUH', 'distrik' => 'BUKITTINGGI'],
            ['kode_sto' => 'PAM', 'nama_sto' => 'PARIAMAN', 'distrik' => 'BUKITTINGGI'],

            // Area Dumai
            ['kode_sto' => 'DUM', 'nama_sto' => 'DUMAI', 'distrik' => 'DUMAI'],
            ['kode_sto' => 'DRI', 'nama_sto' => 'DURI', 'distrik' => 'DUMAI'],
            ['kode_sto' => 'BKN', 'nama_sto' => 'BANGKINANG', 'distrik' => 'DUMAI'],

            // Area Padang
            ['kode_sto' => 'PDC', 'nama_sto' => 'PADANG', 'distrik' => 'PADANG'],
            ['kode_sto' => 'SLK', 'nama_sto' => 'SOLOK', 'distrik' => 'PADANG'],
            ['kode_sto' => 'PNN', 'nama_sto' => 'PAINAN', 'distrik' => 'PADANG'],

            // Area Pekanbaru
            ['kode_sto' => 'PBB', 'nama_sto' => 'PEKANBARU', 'distrik' => 'PEKANBARU'],
            ['kode_sto' => 'ARK', 'nama_sto' => 'ARENGKA', 'distrik' => 'PEKANBARU'],
            ['kode_sto' => 'RBI', 'nama_sto' => 'RUMBAI', 'distrik' => 'PEKANBARU'],
            ['kode_sto' => 'BKR', 'nama_sto' => 'BUKIT RAYA', 'distrik' => 'PEKANBARU'],
            ['kode_sto' => 'PKR', 'nama_sto' => 'PANGKALAN KERINCI', 'distrik' => 'PEKANBARU'],

            // Catatan: Ini adalah sampel representatif dari 121 STO di file.
            // Kamu bisa menambahkan STO lainnya dengan format yang sama sesuai kebutuhan aplikasimu.
        ];

        DB::table('master_stos')->insert($stos);
    }
}
