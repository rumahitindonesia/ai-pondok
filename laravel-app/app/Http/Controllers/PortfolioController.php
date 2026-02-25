<?php

namespace App\Http\Controllers;

use App\Models\Portofolio;
use App\Models\Santri;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function store(Request $request, Santri $santri)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'link_url' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
        ]);

        $santri->portfolios()->create($validated);

        return redirect()->back()->with('success', 'Project berhasil ditambahkan');
    }

    public function update(Request $request, Portofolio $portfolio)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'link_url' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
        ]);

        $portfolio->update($validated);

        return redirect()->back()->with('success', 'Project berhasil diperbarui');
    }

    public function destroy(Portofolio $portfolio)
    {
        $portfolio->delete();

        return redirect()->back()->with('success', 'Project berhasil dihapus');
    }
}
