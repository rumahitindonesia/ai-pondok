<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:kamars,nama',
            'keterangan' => 'nullable|string'
        ]);

        Kamar::create($validated);

        return redirect()->back()->with('success', 'Data kamar berhasil ditambahkan.');
    }

    public function destroy(Kamar $kamar)
    {
        $kamar->delete();
        return redirect()->back()->with('success', 'Data kamar berhasil dihapus.');
    }
}
