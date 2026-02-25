<?php

namespace Database\Seeders;

use App\Models\TagihanSpp;
use App\Models\Pembayaran;
use App\Models\Santri;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $santri = Santri::where('nis', 'SN-PERFECT')->first();
        if (!$santri) return;

        // Create bills and payments for the last 6 months
        for ($i = 0; $i < 6; $i++) {
            $month = Carbon::now()->subMonths($i);
            
            $bill = TagihanSpp::create([
                'santri_id' => $santri->id,
                'bulan' => $month->format('F'),
                'tahun' => $month->year,
                'jumlah_tagihan' => 500000,
                'status' => 'lunas',
            ]);

            Pembayaran::create([
                'tagihan_spp_id' => $bill->id,
                'jumlah_bayar' => 500000,
                'tanggal_bayar' => $month->copy()->addDays(rand(1, 10))->toDateString(),
                'metode' => (rand(0, 1) == 0) ? 'Transfer Bank' : 'Tunai',
                'penerima' => 'Admin Keuangan',
            ]);
        }
    }
}
