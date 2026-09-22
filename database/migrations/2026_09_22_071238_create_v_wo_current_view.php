<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Membuat tabel virtual yang merangkum data dari 3 tabel berbeda
        DB::statement("
            CREATE OR REPLACE VIEW v_wo_current AS
            SELECT
                wo.id AS working_order_id,
                wo.wonum,
                wo.scid,
                wo.nama_cust,
                wo.no_hp,
                wo.alamat,
                wo.jenis_order,
                wo.paket,
                sto.kode_sto,
                sto.nama_sto,
                sto.distrik,
                stat.nama_status AS status_terkini,
                stat.is_final,
                wo.updated_at AS last_update_at
            FROM working_orders wo
            LEFT JOIN master_stos sto ON wo.sto_id = sto.id
            LEFT JOIN master_status_wos stat ON wo.current_status_wo_id = stat.id
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_wo_current");
    }
};
