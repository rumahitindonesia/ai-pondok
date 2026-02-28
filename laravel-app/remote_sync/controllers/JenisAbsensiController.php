<?php

namespace App\Http\Controllers;

use App\Models\JenisAbsensi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JenisAbsensiController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:jenis_absensis,nama',
            'keterangan' => 'nullable|string'
        ]);

        JenisAbsensi::create($validated);

        return redirect()->back()->with('success', 'Kegiatan absensi berhasil ditambahkan.');
    }

    public function destroy(JenisAbsensi $jenisAbsensi)
    {
        $jenisAbsensi->delete();
        return redirect()->back()->with('success', 'Kegiatan absensi berhasil dihapus.');
    }
}
