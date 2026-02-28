<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:kelas,nama',
            'keterangan' => 'nullable|string'
        ]);

        Kelas::create($validated);

        return redirect()->back()->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function destroy(Kelas $kela) // Laravel's route model binding uses singular, so $kela matches 'kelas' parameter
    {
        $kela->delete();
        return redirect()->back()->with('success', 'Data kelas berhasil dihapus.');
    }
}
