<?php

namespace App\Http\Controllers;

use App\Models\JenisPembayaran;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JenisPembayaranController extends Controller
{
    public function index()
    {
        return Inertia::render('Finance/Settings', [
            'jenisPembayarans' => JenisPembayaran::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, JenisPembayaran $jenisPembayaran)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'required|string|max:50|unique:jenis_pembayarans,kode,' . $jenisPembayaran->id,
            'sifat' => 'required|in:bulanan,sekali,cicilan',
            'nominal_default' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $jenisPembayaran->update($validated);

        return redirect()->route('finance.settings')->with('success', 'Jenis pembayaran berhasil diperbarui.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'required|string|max:50|unique:jenis_pembayarans,kode',
            'sifat' => 'required|in:bulanan,sekali,cicilan',
            'nominal_default' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        JenisPembayaran::create($validated);

        return redirect()->route('finance.settings')->with('success', 'Jenis pembayaran berhasil ditambahkan.');
    }

    public function destroy(JenisPembayaran $jenisPembayaran)
    {
        // Prevent deleting if it has related billing records
        if ($jenisPembayaran->tagihanSpps()->count() > 0) {
            return back()->withErrors(['error' => 'Tidak bisa hapus jenis ini karena sudah ada tagihan terkait.']);
        }

        $jenisPembayaran->delete();
        return redirect()->route('finance.settings')->with('success', 'Jenis pembayaran berhasil dihapus.');
    }
}
