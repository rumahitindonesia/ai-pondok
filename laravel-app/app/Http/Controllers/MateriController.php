<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MateriController extends Controller
{
    public function index()
    {
        return Inertia::render('Curriculum/Index', [
            'materis' => Materi::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
        ]);

        Materi::create($validated);

        return redirect()->back()->with('success', 'Materi berhasil ditambahkan');
    }

    public function update(Request $request, Materi $materi)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
        ]);

        $materi->update($validated);

        return redirect()->back()->with('success', 'Materi berhasil diperbarui');
    }

    public function destroy(Materi $materi)
    {
        $materi->delete();

        return redirect()->back()->with('success', 'Materi berhasil dihapus');
    }
}
