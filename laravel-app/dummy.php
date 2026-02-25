<?php
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// Create a dummy Tagihan
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

// Today's payment
DB::table('pembayarans')->insert([
    'tagihan_id' => $tagihanId,
    'tanggal_bayar' => Carbon::now(),
    'jumlah_bayar' => 250000,
    'metode_pembayaran' => 'transfer',
    'created_at' => now(),
    'updated_at' => now(),
]);

// Yesterday's payment
DB::table('pembayarans')->insert([
    'tagihan_id' => $tagihanId,
    'tanggal_bayar' => Carbon::now()->subDays(1),
    'jumlah_bayar' => 150000,
    'metode_pembayaran' => 'tunai',
    'created_at' => now(),
    'updated_at' => now(),
]);

// 2 Days ago payment
DB::table('pembayarans')->insert([
    'tagihan_id' => $tagihanId,
    'tanggal_bayar' => Carbon::now()->subDays(2),
    'jumlah_bayar' => 300000,
    'metode_pembayaran' => 'transfer',
    'created_at' => now(),
    'updated_at' => now(),
]);

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Dummy data inserted successfully.\n";
