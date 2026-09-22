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
    Schema::create('master_stos', function (Blueprint $table)
    {
        $table->id();
        $table->string('kode_sto', 20)->unique();
        $table->string('nama_sto', 100)->nullable();
        $table->string('distrik', 50);
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_stos');
    }
};
