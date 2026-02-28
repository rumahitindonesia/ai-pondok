<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\WaliSantri;
use App\Models\Kelas;
use App\Models\Kamar;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SantriController extends Controller
{
    public function index(Request $request)
    {
        $query = Santri::query()->with('wali');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->input('kelas'));
        }
        if ($request->filled('kamar')) {
            $query->where('kamar', $request->input('kamar'));
        }
        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->input('angkatan'));
        }
        if ($request->filled('entitas')) {
            $query->where('entitas', $request->input('entitas'));
        }

        // Sorting
        $sortField = $request->input('sort_by', 'created_at');
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['nama', 'nis', 'kelas', 'kamar', 'status', 'created_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }
        $sortDir = $request->input('sort_dir', 'desc');

        $query->orderBy($sortField, $sortDir);

        $santris = $query->paginate(10)->withQueryString();

        // Filter Options
        $availableKelas = Kelas::orderBy('nama')->pluck('nama');
        $availableKamar = Kamar::orderBy('nama')->pluck('nama');
        $availableAngkatan = Santri::whereNotNull('angkatan')->distinct()->orderBy('angkatan')->pluck('angkatan');
        $availableEntitas = Santri::whereNotNull('entitas')->where('entitas', '!=', '')->distinct()->orderBy('entitas')->pluck('entitas');

        // Summary Stats
        $stats = [
            'total' => Santri::count(),
            'aktif' => Santri::where('status', 'Santri Aktif')->count(),
            'alumni' => Santri::where('status', 'Santri Lulus - Alumni')->count(),
            'keluar' => Santri::where('status', 'Santri Keluar')->count(),
        ];

        return Inertia::render('Santri/Index', [
            'santris' => $santris,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'kelas', 'kamar', 'angkatan', 'entitas', 'sort_by', 'sort_dir']),
            'options' => [
                'kelas' => $availableKelas,
                'kamar' => $availableKamar,
                'angkatan' => $availableAngkatan,
                'entitas' => $availableEntitas,
            ]
        ]);
    }

    public function create()
    {
        $walis = WaliSantri::all();
        $kelas = Kelas::orderBy('nama')->get();
        $kamars = Kamar::orderBy('nama')->get();

        return Inertia::render('Santri/Create', [
            'walis' => $walis,
            'kelas' => $kelas,
            'kamars' => $kamars
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'nullable|string|max:20|unique:santris',
            'jenis_kelamin' => 'required|in:L,P',
            'wali_id' => 'nullable|exists:wali_santris,id',
            'status' => 'required|in:Santri Aktif,Santri Keluar,Santri Lulus - Alumni,Alumni Aktif - dipondok,Alumni Tidak Aktif - diluar pondok,Santri Magang,Alumni Magang',
            'kelas' => 'nullable|string',
            'kamar' => 'nullable|string',
            'entitas' => 'nullable|string',
            'angkatan' => 'nullable|integer',
            'bio' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|max:2048',
            'is_portfolio_featured' => 'boolean',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('santri', 'public');
        }

        Santri::create($validated);

        return redirect()->route('santri.index')->with('success', 'Data santri berhasil ditambahkan');
    }

    public function show(Santri $santri)
    {
        $santri->load(['wali', 'absensis', 'pelanggarans', 'tagihanSpps', 'achievements.materi', 'portfolios']);
        
        return Inertia::render('Santri/Show', [
            'santri' => $santri,
            'materis' => Materi::all()
        ]);
    }

    public function edit(Santri $santri)
    {
        $walis = WaliSantri::all();
        $kelas = Kelas::orderBy('nama')->get();
        $kamars = Kamar::orderBy('nama')->get();

        return Inertia::render('Santri/Edit', [
            'santri' => $santri,
            'walis' => $walis,
            'kelas' => $kelas,
            'kamars' => $kamars
        ]);
    }

    public function update(Request $request, Santri $santri)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'nullable|string|max:20|unique:santris,nis,' . $santri->id,
            'jenis_kelamin' => 'required|in:L,P',
            'wali_id' => 'nullable|exists:wali_santris,id',
            'status' => 'required|in:Santri Aktif,Santri Keluar,Santri Lulus - Alumni,Alumni Aktif - dipondok,Alumni Tidak Aktif - diluar pondok,Santri Magang,Alumni Magang',
            'kelas' => 'nullable|string',
            'kamar' => 'nullable|string',
            'entitas' => 'nullable|string',
            'angkatan' => 'nullable|integer',
            'bio' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|max:2048',
            'is_portfolio_featured' => 'boolean',
        ]);

        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($santri->foto) {
                Storage::disk('public')->delete($santri->foto);
            }
            $validated['foto'] = $request->file('foto')->store('santri', 'public');
        }

        $santri->update($validated);

        return redirect()->route('santri.index')->with('success', 'Data santri berhasil diperbarui');
    }

    public function destroy(Santri $santri)
    {
        if ($santri->foto) {
            Storage::disk('public')->delete($santri->foto);
        }
        $santri->delete();
        return redirect()->route('santri.index')->with('success', 'Data santri berhasil dihapus');
    }

    public function settings()
    {
        return Inertia::render('Santri/Settings/Index', [
            'kelas' => Kelas::orderBy('nama')->get(),
            'kamars' => Kamar::orderBy('nama')->get(),
        ]);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:santris,id',
            'status' => 'required|in:Santri Aktif,Santri Keluar,Santri Lulus - Alumni,Alumni Aktif - dipondok,Alumni Tidak Aktif - diluar pondok,Santri Magang,Alumni Magang',
        ]);

        Santri::whereIn('id', $validated['ids'])->update([
            'status' => $validated['status']
        ]);

        return redirect()->back()->with('success', count($validated['ids']) . ' santri berhasil diperbarui statusnya.');
    }
}
