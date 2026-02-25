<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hubungan = ['Ayah', 'Ibu', 'Wali'];
        $jk = ['L', 'P'];
        $status = ['aktif', 'alumni', 'keluar'];

        for ($i = 1; $i <= 5; $i++) {
            $wali = \App\Models\WaliSantri::create([
                'nama' => 'Wali Santri ' . $i,
                'hubungan' => $hubungan[array_rand($hubungan)],
                'no_hp' => '0812345678' . $i,
                'alamat' => 'Alamat Wali ' . $i,
                'email' => 'wali' . $i . '@example.com',
            ]);

            for ($j = 1; $j <= 4; $j++) {
                \App\Models\Santri::create([
                    'nis' => 'SN' . $i . $j . rand(100, 999),
                    'nama' => 'Santri Ke-' . (($i-1)*4 + $j),
                    'jenis_kelamin' => $jk[array_rand($jk)],
                    'wali_id' => $wali->id,
                    'status' => $status[array_rand($status)],
                    'kelas' => rand(1, 6) . ' SD',
                    'kamar' => 'Kamar ' . chr(64 + rand(1, 5)),
                ]);
            }
        }
    }
}
