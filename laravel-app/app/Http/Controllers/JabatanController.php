<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JabatanController extends Controller
{
    /**
     * Display a listing of the jabatans (hierarchical view).
     */
    public function index()
    {
        // Get all top-level jabatans (where parent_id is null) with their nested children
        $jabatans = Jabatan::whereNull('parent_id')
            ->with('children.children.children') // eager load a few levels deep
            ->get();
            
        // Get generic flat list for dropdowns (when creating a new child)
        $allJabatans = Jabatan::select('id', 'nama', 'parent_id')->get();

        return Inertia::render('Organisasi/Jabatan/Index', [
            'jabatansTree' => $jabatans,
            'allJabatans' => $allJabatans
        ]);
    }

    /**
     * Store a newly created jabatan in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:jabatans,id',
            'deskripsi' => 'nullable|string'
        ]);

        Jabatan::create($validated);

        return redirect()->back()->with('success', 'Jabatan berhasil ditambahkan.');
    }

    /**
     * Update the specified jabatan in storage.
     */
    public function update(Request $request, Jabatan $jabatan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:jabatans,id',
            'deskripsi' => 'nullable|string'
        ]);

        // Prevent self-referencing loop
        if ($validated['parent_id'] == $jabatan->id) {
            return back()->withErrors(['parent_id' => 'Jabatan tidak bisa menjadi induk bagi dirinya sendiri.']);
        }

        $jabatan->update($validated);

        return redirect()->back()->with('success', 'Jabatan berhasil diperbarui.');
    }

    /**
     * Remove the specified jabatan from storage.
     */
    public function destroy(Jabatan $jabatan)
    {
        // Optional: Check if it has children before deleting, or let cascade/nullOnDelete handle it based on migration
        if ($jabatan->children()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus jabatan yang memiliki bawahan. Pindahkan bawahan terlebih dahulu.');
        }
        
        // Also check if there are people assigned to this
        if ($jabatan->pengurus()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus jabatan karena masih ada pengurus yang menjabat.');
        }

        $jabatan->delete();

        return redirect()->back()->with('success', 'Jabatan berhasil dihapus.');
    }
}

