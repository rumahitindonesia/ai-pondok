<?php

namespace Database\Seeders;

use App\Models\Santri;
use App\Models\WaliSantri;
use App\Models\Kelas;
use App\Models\Kamar;
use Illuminate\Database\Seeder;

class PerfectSantriSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing perfect dummy to avoid conflicts
        Santri::where('nis', 'SN-PERFECT')->delete();

        // 1. Create/Update Wali - very aggressive purge to avoid unique constraint mess
        $no_hp = '081234567890';
        $email = 'fatih_wali_perfect@pondok.id';
        
        WaliSantri::where('no_hp', $no_hp)->delete();
        WaliSantri::where('email', $email)->delete();
        
        $wali = WaliSantri::create([
            'nama_ayah' => 'H. Ahmad Syarifuddin',
            'no_hp' => $no_hp,
            'email' => $email,
        ]);

        // 2. Create Kelas
        $kelas = Kelas::firstOrCreate(['nama_kelas' => '12-IPA-A']);

        // 3. Create Kamar
        $kamar = Kamar::firstOrCreate([
            'nama_kamar' => 'Zaid Bin Tsabit',
            'kapasitas' => 10
        ]);

        // 4. Create Perfect Santri
        Santri::create([
            'nis' => 'SN-PERFECT',
            'nama' => 'Muhammad Al-Fatih',
            'jenis_kelamin' => 'Laki-laki',
            'tempat_lahir' => 'Istanbul',
            'tanggal_lahir' => '2008-03-29',
            'alamat' => 'Jl. Kebangkitan No. 1453, Kesultanan BSD',
            'wali_id' => $wali->id,
            'kelas_id' => $kelas->id,
            'kamar_id' => $kamar->id,
            'status' => 'aktif',
            'bio' => "Santri teladan yang bercita-cita menjadi Hafiz Quran sekaligus Insinyur Robotika. Sangat tekun dalam mempelajari kitab kuning dan memiliki ketertarikan tinggi pada pengembangan teknologi AI untuk kemaslahatan umat.",
            'minat_bakat' => "Tahfidz, Robotika, Futsal, Kaligrafi",
            'cita_cita' => "Hafiz & AI Engineer",
            'email' => 'fatih_perfect@pondok.id',
            'no_hp' => '089988776655',
        ]);
    }
}
