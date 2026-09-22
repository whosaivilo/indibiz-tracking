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
    Schema::create('wo_tracking_logs', function (Blueprint $table) {
        $table->id();

        // Relasi ke tabel working_orders
        $table->foreignId('working_order_id')->constrained('working_orders')->onDelete('cascade');

        // Relasi ke tabel users (bisa null kalau ditarik dari CSV)
        $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

        // Relasi ke tabel master (sementara kita buat nullable dulu)
        $table->foreignId('status_wo_id')->nullable()->constrained('master_status_wos')->onDelete('set null');
        $table->foreignId('alasan_kendala_id')->nullable()->constrained('master_alasan_kendalas')->onDelete('set null');

        // Tangkapan teks asli dari CSV untuk proses komparasi
        $table->string('status_wo_raw', 100)->nullable();
        $table->text('catatan')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wo_tracking_logs');
    }
};
