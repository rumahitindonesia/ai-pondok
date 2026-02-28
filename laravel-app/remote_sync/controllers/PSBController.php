<?php

namespace App\Http\Controllers;

use App\Models\PsbRegistration;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class PSBController extends Controller
{
    /**
     * Public registration form.
     */
    public function register()
    {
        return Inertia::render('PSB/Register');
    }

    /**
     * Store public registration submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_calon' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'nama_wali' => 'required|string|max:255',
            'no_hp_wali' => 'required|string|max:20',
            'berkas_kk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'berkas_akta' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'berkas_ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $validated;

        // Handle file uploads
        foreach (['berkas_kk', 'berkas_akta', 'berkas_ijazah'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('psb_documents', 'public');
            }
        }

        PsbRegistration::create($data);

        return redirect()->route('psb.success')->with('success', 'Pendaftaran berhasil dikirim!');
    }

    /**
     * Admin dashboard for registration management.
     */
    public function adminIndex()
    {
        $registrations = PsbRegistration::latest()->paginate(10);
        return Inertia::render('PSB/AdminIndex', [
            'registrations' => $registrations
        ]);
    }

    /**
     * Update selection status.
     */
    public function updateStatus(Request $request, PsbRegistration $psb)
    {
        $validated = $request->validate([
            'status_seleksi' => 'required|in:pending,diterima,cadangan,ditolak'
        ]);

        $psb->update($validated);

        return back()->with('success', 'Status pendaftaran berhasil diperbarui');
    }

    public function success()
    {
        return Inertia::render('PSB/Success');
    }
}
