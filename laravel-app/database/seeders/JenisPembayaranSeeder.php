<?php

namespace Database\Seeders;

use App\Models\JenisPembayaran;
use Illuminate\Database\Seeder;

class JenisPembayaranSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'nama' => 'SPP Bulanan',
                'kode' => 'spp',
                'sifat' => 'bulanan',
                'nominal_default' => 500000,
                'keterangan' => 'Iuran bulanan wajib santri aktif',
            ],
            [
                'nama' => 'Pendaftaran',
                'kode' => 'pendaftaran',
                'sifat' => 'sekali',
                'nominal_default' => 1000000,
                'keterangan' => 'Biaya pendaftaran masuk pesantren, dibayar sekali',
            ],
            [
                'nama' => 'Wakaf Bangunan & Fasilitas',
                'kode' => 'wakaf',
                'sifat' => 'cicilan',
                'nominal_default' => 5000000,
                'keterangan' => 'Wakaf untuk pembangunan dan fasilitas pesantren, bisa dicicil',
            ],
        ];

        foreach ($types as $type) {
            JenisPembayaran::firstOrCreate(['kode' => $type['kode']], $type);
        }
    }
}
