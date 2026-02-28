<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE santris MODIFY COLUMN status VARCHAR(255) DEFAULT 'Santri Aktif'");
        DB::table('santris')->where('status', 'aktif')->update(['status' => 'Santri Aktif']);
        DB::table('santris')->where('status', 'keluar')->update(['status' => 'Santri Keluar']);
        DB::table('santris')->where('status', 'alumni')->update(['status' => 'Santri Lulus - Alumni']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('santris')->where('status', 'Santri Aktif')->update(['status' => 'aktif']);
        DB::table('santris')->whereIn('status', ['Santri Keluar'])->update(['status' => 'keluar']);
        DB::table('santris')->whereIn('status', ['Santri Lulus - Alumni', 'Alumni Aktif - dipondok', 'Alumni Tidak Aktif - diluar pondok', 'Alumni Magang'])->update(['status' => 'alumni']);
        DB::table('santris')->where('status', 'Santri Magang')->update(['status' => 'aktif']);

        DB::statement("ALTER TABLE santris MODIFY COLUMN status ENUM('aktif', 'alumni', 'keluar') DEFAULT 'aktif'");
    }
};
