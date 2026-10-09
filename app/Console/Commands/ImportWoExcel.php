<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\WorkingOrderImport; // Memanggil folder Imports yang baru terbuat

class ImportWoExcel extends Command
{
    // Tambahkan {region} agar kita bisa menentukan wilayah dari terminal
    protected $signature = 'import:wo-excel {region}'; 
    protected $description = 'Import data WO dari Excel berdasarkan Region (batam/sumbar/ridar)';

    public function handle()
    {
        // Tambahkan 2 baris ini untuk mem-bypass batas memori dan batas waktu PHP
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        // Menangkap teks region yang diketik di terminal
        $region = strtolower($this->argument('region'));
        $this->info("Memulai proses import Excel untuk region: {$region}...");

        // Sistem secara cerdas memilih file yang tepat
        if ($region === 'batam') {
            $filePath = storage_path('app/PROV 2026 - DATA PROGRES.csv');
        } elseif ($region === 'sumbar') {
            $filePath = storage_path('app/PROGRES PROV SUMBAR V5 - ALL WO HARIAN (1).csv');
        } elseif ($region === 'ridar') {
            $filePath = storage_path('app/LOGBOOK PROVISIONING RIDAR - PBB.csv');
        } else {
            $this->error("Region tidak dikenali! Gunakan 'batam', 'sumbar', atau 'ridar'.");
            return;
        }

        if (!file_exists($filePath)) {
            $this->error("File untuk region {$region} tidak ditemukan di folder storage!");
            return;
        }

        // Panggil mesin pembaca dan kirimkan parameter region-nya
        Excel::import(new WorkingOrderImport($region), $filePath);

        $this->info("Import data region {$region} sukses dieksekusi!");
    }
}