<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Santri;
use App\Models\JenisAbsensi;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $santri = Santri::where('nis', 'SN-PERFECT')->first();
        if (!$santri) return;

        $types = JenisAbsensi::all();
        if ($types->isEmpty()) {
            $types = collect([
                JenisAbsensi::create(['nama_kegiatan' => 'Sholat Subuh Berjamaah']),
                JenisAbsensi::create(['nama_kegiatan' => 'Kajian Kitab Kuning']),
                JenisAbsensi::create(['nama_kegiatan' => 'Tahfidz Quran']),
            ]);
        }

        // Create 30 days of attendance
        for ($i = 0; $i < 30; $i++) {
            foreach ($types as $type) {
                Absensi::create([
                    'santri_id' => $santri->id,
                    'jenis_absensi_id' => $type->id,
                    'tanggal' => Carbon::now()->subDays($i)->toDateString(),
                    'status' => (rand(0, 10) > 1) ? 'hadir' : 'absen', // 90% attendance
                    'keterangan' => 'Hadir tepat waktu',
                ]);
            }
        }
    }
}
