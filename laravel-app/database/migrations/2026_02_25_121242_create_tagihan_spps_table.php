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
        Schema::create('tagihan_spps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->onDelete('cascade');
            $table->string('bulan'); // Januari, Februari, etc
            $table->integer('tahun');
            $table->decimal('jumlah', 15, 2);
            $table->date('jatuh_tempo');
            $table->enum('status', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_spps');
    }
};
