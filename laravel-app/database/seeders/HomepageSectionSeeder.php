<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HomepageSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'section_key' => 'hero',
                'title' => 'Lembaga Pendidikan IT Berbasis Pondok',
                'subtitle' => 'Mencetak Generasi Qurani yang Ahli di Bidang Teknologi',
                'content' => [
                    'description' => 'Rumah IT Indonesia mengajarkan Agama dan Skill IT sekaligus untuk semua santrinya baik laki-laki maupun perempuan.',
                    'cta_text' => 'Daftar Sekarang',
                ],
                'is_active' => true,
            ],
            [
                'section_key' => 'programs',
                'title' => 'Program Pendidikan Kami',
                'subtitle' => 'Kurikulum Terpadu Ikhwan & Akhwat',
                'content' => [
                    'ikhwan' => [
                        'title' => 'Santri Laki-laki (Ikhwan)',
                        'tracks' => ['Programmer', 'Bisnis Digital'],
                    ],
                    'akhwat' => [
                        'title' => 'Santri Perempuan (Akhwat)',
                        'tracks' => ['Bisnis Digital'],
                    ],
                ],
                'is_active' => true,
            ],
            [
                'section_key' => 'tracks',
                'title' => 'Pilihan Jalur Keahlian',
                'subtitle' => 'Skill IT yang Diajarkan Secara Mendalam',
                'content' => [
                    'programmer' => [
                        'name' => 'Programmer',
                        'specializations' => ['Front-End', 'Back-End', 'Mobile Development'],
                    ],
                    'business' => [
                        'name' => 'Bisnis Digital',
                        'specializations' => ['Content Creation', 'Admin Media Sosial', 'Customer Service', 'Ads (Meta/Google)', 'TikTok Affiliate'],
                    ],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($sections as $section) {
            \App\Models\HomepageSection::updateOrCreate(
                ['section_key' => $section['section_key']],
                $section
            );
        }
    }
}
