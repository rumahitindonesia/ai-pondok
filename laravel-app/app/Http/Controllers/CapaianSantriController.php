<?php

namespace App\Http\Controllers;

use App\Models\CapaianSantri;
use App\Models\Santri;
use Illuminate\Http\Request;

class CapaianSantriController extends Controller
{
    public function store(Request $request, Santri $santri)
    {
        $validated = $request->validate([
            'materi_id' => 'required|exists:materis,id',
            'tanggal_selesai' => 'nullable|date',
            'nilai' => 'nullable|integer|min:0|max:100',
        ]);

        $santri->achievements()->updateOrCreate(
            ['materi_id' => $validated['materi_id']],
            $validated
        );

        return redirect()->back()->with('success', 'Pencapaian berhasil dicatat');
    }

    public function destroy(CapaianSantri $achievement)
    {
        $achievement->delete();

        return redirect()->back()->with('success', 'Pencapaian berhasil dihapus');
    }
}
