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
    Schema::create('master_status_wos', function (Blueprint $table) {
        $table->id();
        $table->string('nama_status', 150);
        $table->boolean('is_final')->default(0); // Tipe boolean otomatis jadi 0 atau 1 di MySQL
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_status_wos');
    }
};
