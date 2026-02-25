<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        $tagihanId = DB::table('tagihan_spps')->insertGetId([
            'santri_id' => 1,
            'bulan' => 'Februari',
            'tahun' => Carbon::now()->year,
            'jumlah' => 500000,
            'jatuh_tempo' => Carbon::now()->addDays(10),
            'status' => 'belum_lunas',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pembayarans')->insert([
            'tagihan_id' => $tagihanId,
            'tanggal_bayar' => Carbon::now(),
            'jumlah_bayar' => 250000,
            'metode_pembayaran' => 'transfer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pembayarans')->insert([
            'tagihan_id' => $tagihanId,
            'tanggal_bayar' => Carbon::now()->subDays(1),
            'jumlah_bayar' => 150000,
            'metode_pembayaran' => 'tunai',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pembayarans')->insert([
            'tagihan_id' => $tagihanId,
            'tanggal_bayar' => Carbon::now()->subDays(2),
            'jumlah_bayar' => 300000,
            'metode_pembayaran' => 'transfer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
