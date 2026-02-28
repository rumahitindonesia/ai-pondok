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
        Schema::table('santris', function (Blueprint $table) {
            $table->index('nama');
            $table->index('nis');
            $table->index('status');
            $table->index('kelas');
            $table->index('kamar');
            $table->index('entitas');
            $table->index('angkatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->dropIndex(['nama']);
            $table->dropIndex(['nis']);
            $table->dropIndex(['status']);
            $table->dropIndex(['kelas']);
            $table->dropIndex(['kamar']);
            $table->dropIndex(['entitas']);
            $table->dropIndex(['angkatan']);
        });
    }
};
