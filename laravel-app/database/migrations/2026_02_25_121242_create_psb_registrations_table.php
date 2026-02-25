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
        Schema::create('psb_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('nama_calon');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nama_wali');
            $table->string('no_hp_wali');
            $table->string('berkas_kk')->nullable();
            $table->string('berkas_akta')->nullable();
            $table->string('berkas_ijazah')->nullable();
            $table->enum('status_seleksi', ['pending', 'diterima', 'cadangan', 'ditolak'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psb_registrations');
    }
};
