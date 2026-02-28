<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Pengurus;
use App\Models\Santri;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PengurusController extends Controller
{
    /**
     * Display a listing of the pengurus assignments.
     */
    public function index()
    {
        // Load pengurus with their related Santri and Jabatan names
        $pengurus = Pengurus::with(['santri:id,nama_lengkap,nis,foto', 'jabatan:id,nama,parent_id'])
            ->orderBy('status', 'desc') // Active first
            ->latest()
            ->get();
            
        // For the assignment dropdowns
        $jabatans = Jabatan::select('id', 'nama', 'parent_id')->orderBy('nama')->get();
        // Get all santris that can be assigned
        $santris = Santri::select('id', 'nama_lengkap', 'nis')->orderBy('nama_lengkap')->get();

        return Inertia::render('Organisasi/Pengurus/Index', [
            'pengurusList' => $pengurus,
            'jabatans' => $jabatans,
            'santris' => $santris
        ]);
    }

    /**
     * Store a newly created pengurus assignment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'jabatan_id' => 'required|exists:jabatans,id',
            'status' => 'boolean'
        ]);

        // Optional: Check if santri already has THIS active jabatan to prevent exact duplicates
        $exists = Pengurus::where('santri_id', $validated['santri_id'])
                          ->where('jabatan_id', $validated['jabatan_id'])
                          ->where('status', true)
                          ->exists();
                          
        if ($exists) {
            return back()->withErrors(['santri_id' => 'Santri ini sudah aktif menjabat di posisi tersebut.']);
        }

        Pengurus::create([
            'santri_id' => $validated['santri_id'],
            'jabatan_id' => $validated['jabatan_id'],
            'status' => $validated['status'] ?? true
        ]);

        return redirect()->back()->with('success', 'Pengurus berhasil ditambahkan.');
    }

    /**
     * Update the assignment (usually toggling active status).
     */
    public function update(Request $request, Pengurus $penguru) // Laravel sometimes singularizes to penguru
    {
        $validated = $request->validate([
            'status' => 'required|boolean'
        ]);

        $penguru->update($validated);

        return redirect()->back()->with('success', 'Status pengurus berhasil diperbarui.');
    }

    /**
     * Remove the assignment entirely.
     */
    public function destroy(Pengurus $penguru)
    {
        $penguru->delete();

        return redirect()->back()->with('success', 'Data pengurus berhasil dihapus.');
    }
}

