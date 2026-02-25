<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Santri;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PelanggaranController extends Controller
{
    public function index()
    {
        $pelanggarans = Pelanggaran::with('santri')->latest()->get();
        return Inertia::render('Records/Discipline', [
            'pelanggarans' => $pelanggarans
        ]);
    }

    public function create()
    {
        $santris = Santri::where('status', 'aktif')->get();
        return Inertia::render('Records/CreateDiscipline', [
            'santris' => $santris
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'judul_pelanggaran' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'poin' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        Pelanggaran::create($validated);

        return redirect()->route('records.discipline')->with('success', 'Pelanggaran berhasil dicatat.');
    }

    public function destroy(Pelanggaran $pelanggaran)
    {
        $pelanggaran->delete();
        return redirect()->back()->with('success', 'Catatan pelanggaran berhasil dihapus.');
    }
}
