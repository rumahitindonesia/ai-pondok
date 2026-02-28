<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            ['question_text' => 'Upload Foto ukuran 4x6', 'type' => 'file', 'options' => null, 'is_required' => true],
            ['question_text' => 'Tanggal Lahir', 'type' => 'date', 'options' => null, 'is_required' => false],
            ['question_text' => 'Jurusan yang dituju', 'type' => 'radio', 'options' => ['Programmer (Khusus Ikhwan)', 'Bisnis Digital'], 'is_required' => true],
            ['question_text' => 'Cita-Cita', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => 'Sudah Lulus / Masih Sekolah', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => 'Provinsi', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => 'Hobi', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => 'Alamat Rumah', 'type' => 'textarea', 'options' => null, 'is_required' => true],
            ['question_text' => 'Link Sosial Media (Facebook/Instagram)', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => 'Email', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => 'Lulusan', 'type' => 'radio', 'options' => ['SMP', 'SMA', 'SMK', 'PONDOK', 'Yang lain'], 'is_required' => true],
            ['question_text' => 'Pengalaman Organisasi', 'type' => 'textarea', 'options' => null, 'is_required' => false],
            ['question_text' => 'Nama Sekolah', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => 'Jurusan di Sekolah', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => 'Prestasi', 'type' => 'textarea', 'options' => null, 'is_required' => false],
            ['question_text' => 'Kondisi Orang Tua', 'type' => 'radio', 'options' => ['Lengkap', 'Yatim', 'Piatu', 'Yatim Piatu', 'Cerai Hidup'], 'is_required' => true],
            ['question_text' => 'Nama Ayah', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => 'Pekerjaan Ayah', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => 'Nama Ibu', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => 'Pekerjaan Ibu', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => 'Gaji Orang Tua', 'type' => 'radio', 'options' => ['< Rp 1.000.000', 'Rp 1.000.000 - Rp 2.500.000', 'Rp 2.500.000 - Rp 5.000.000', '> Rp 5.000.000'], 'is_required' => true],
            ['question_text' => 'Jumlah Saudara', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => 'Punya Laptop / Tidak', 'type' => 'radio', 'options' => ['Punya', 'Belum', 'Sedang Saya Usahakan'], 'is_required' => true],
            ['question_text' => 'Hafalan Al-Qur\'an (berapa juz)', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => '3 Ustadz Favorite', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => 'Memiliki Pacar / Tidak', 'type' => 'radio', 'options' => ['Ya', 'Tidak'], 'is_required' => true],
            ['question_text' => 'Pemahaman Agama', 'type' => 'radio', 'options' => ['Salaf', 'Muhammadiyah', 'Nu', 'Yang lain'], 'is_required' => true],
            ['question_text' => 'Dari Mana Anda Mengetahui Rumah IT Hub?', 'type' => 'radio', 'options' => ['Orang Tua', 'Guru/Ustadz', 'Saudara', 'Teman', 'Facebook', 'Youtube', 'Instagram', 'Yang lain'], 'is_required' => true],
            ['question_text' => 'Punya BPJS / tidak', 'type' => 'radio', 'options' => ['Punya', 'Tidak'], 'is_required' => true],
            ['question_text' => 'Tokoh Idola', 'type' => 'text', 'options' => null, 'is_required' => true],
            ['question_text' => 'Masih Merokok?', 'type' => 'radio', 'options' => ['Iya', 'Bukan Perokok', 'Berusaha Berhenti', 'Kadang-kadang', 'Sudah Berhenti Total'], 'is_required' => true],
            ['question_text' => 'Pernah Belajar di Jurusan yang dituju?', 'type' => 'radio', 'options' => ['Pernah', 'Tidak Pernah'], 'is_required' => true],
            ['question_text' => 'Amalan Sunnah yang sering dilakukan', 'type' => 'textarea', 'options' => null, 'is_required' => true],
            ['question_text' => 'Jelaskan Tentang Skill IT yang sudah dimiliki!', 'type' => 'textarea', 'options' => null, 'is_required' => true],
            ['question_text' => 'Pelajaran yang Anda Sukai', 'type' => 'text', 'options' => null, 'is_required' => true],
        ];

        foreach ($questions as $index => $q) {
            \App\Models\FormQuestion::create(array_merge($q, ['order_num' => $index + 1]));
        }
    }
}
