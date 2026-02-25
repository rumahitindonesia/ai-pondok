<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan_spps', function (Blueprint $table) {
            $table->foreignId('jenis_pembayaran_id')
                  ->nullable()
                  ->constrained('jenis_pembayarans')
                  ->nullOnDelete()
                  ->after('santri_id');
            $table->string('keterangan')->nullable()->after('jatuh_tempo');
            // make bulan & tahun nullable (not needed for non-monthly types)
            $table->string('bulan')->nullable()->change();
            $table->integer('tahun')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_spps', function (Blueprint $table) {
            $table->dropForeign(['jenis_pembayaran_id']);
            $table->dropColumn(['jenis_pembayaran_id', 'keterangan']);
            $table->string('bulan')->nullable(false)->change();
            $table->integer('tahun')->nullable(false)->change();
        });
    }
};
