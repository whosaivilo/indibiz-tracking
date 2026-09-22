<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('working_orders', function (Blueprint $table) {
        $table->id();
        $table->string('wonum', 50)->nullable()->unique();
        $table->enum('jenis_tiket', ['WO', 'INC', 'MANUAL'])->default('WO');
        $table->text('scid');

        // Data Pelanggan (Denormalisasi dari CSV)
        $table->string('nama_cust', 150)->nullable();
        $table->string('no_hp', 25)->nullable();
        $table->text('alamat')->nullable();

        // Relasi STO
        $table->string('sto_raw', 50)->nullable(); // Tangkapan asli CSV
        $table->foreignId('sto_id')->nullable()->constrained('master_stos')->onDelete('set null');

        $table->string('jenis_order', 50)->nullable();
        $table->string('paket', 150)->nullable();
        $table->dateTime('date_create')->nullable();

        // Status Terkini (Untuk View/Dashboard Cepat)
        $table->foreignId('current_status_wo_id')->nullable()->constrained('master_status_wos')->onDelete('set null');
        $table->dateTime('last_update_at')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_orders');
    }
};
