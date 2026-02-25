<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // e.g. "SPP Bulanan"
            $table->string('kode')->unique(); // e.g. "spp", "pendaftaran", "wakaf"
            $table->enum('sifat', ['bulanan', 'sekali', 'cicilan'])->default('sekali');
            $table->unsignedBigInteger('nominal_default')->nullable(); // default amount
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_pembayarans');
    }
};
