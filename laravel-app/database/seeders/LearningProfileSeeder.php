<?php

namespace Database\Seeders;

use App\Models\Materi;
use App\Models\CapaianSantri;
use App\Models\Portofolio;
use App\Models\Santri;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LearningProfileSeeder extends Seeder
{
    public function run(): void
    {
        $santri = Santri::where('nis', 'SN11402')->first();
        if (!$santri) return;

        // 1. Create Materials
        $materis = [
            ['nama' => 'Laravel Framework', 'kategori' => 'IT', 'icon' => 'laravel'],
            ['nama' => 'Vue.js Frontend', 'kategori' => 'IT', 'icon' => 'vue'],
            ['nama' => 'Kitab Al-Ajurumiyah', 'kategori' => 'Kitab', 'icon' => 'kitab'],
            ['nama' => 'Tahfidz Juz 30', 'kategori' => 'Al-Quran', 'icon' => 'quran'],
            ['nama' => 'Tailwind CSS', 'kategori' => 'IT', 'icon' => 'tailwind'],
        ];

        foreach ($materis as $m) {
            $materi = Materi::firstOrCreate(['nama' => $m['nama']], $m);
            
            CapaianSantri::firstOrCreate([
                'santri_id' => $santri->id,
                'materi_id' => $materi->id,
            ], [
                'tanggal_selesai' => Carbon::now()->subMonths(rand(1, 6))->toDateString(),
                'nilai' => rand(85, 100),
            ]);
        }

        // 2. Create Portfolios
        $portfolios = [
            [
                'judul' => 'Sistem Management Pondok',
                'deskripsi' => 'Membangun sistem ERP sederhana untuk pengelolaan data santri dan keuangan menggunakan Laravel & Vue.',
                'link_url' => 'https://github.com/santri/pondok-it',
                'tanggal' => Carbon::now()->subMonths(2)->toDateString(),
            ],
            [
                'judul' => 'Landing Page PSB',
                'deskripsi' => 'Desain dan implementasi landing page pendaftaran santri baru dengan integrasi iPaymu.',
                'link_url' => '#',
                'tanggal' => Carbon::now()->subMonths(4)->toDateString(),
            ],
        ];

        foreach ($portfolios as $p) {
            Portofolio::firstOrCreate([
                'santri_id' => $santri->id,
                'judul' => $p['judul']
            ], $p);
        }
    }
}
