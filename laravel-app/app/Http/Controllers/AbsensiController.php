<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Santri;
use App\Models\JenisAbsensi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $activities = JenisAbsensi::all();
        $firstActivity = $activities->first()?->nama ?? 'sholat';

        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $jenis = $request->input('jenis', $firstActivity);
        $kelas = $request->input('kelas');
        $kamar = $request->input('kamar');

        $query = Santri::where('status', 'aktif');

        if ($kelas) {
            $query->where('kelas', $kelas);
        }
        if ($kamar) {
            $query->where('kamar', $kamar);
        }

        $santrisList = $query->get();

        $absensis = Absensi::where('tanggal', $tanggal)
            ->where('jenis', $jenis)
            ->get()
            ->keyBy('santri_id');

        // Get unique classes and rooms for filters
        $availableKelas = Santri::where('status', 'aktif')->whereNotNull('kelas')->distinct()->pluck('kelas')->sort();
        $availableKamar = Santri::where('status', 'aktif')->whereNotNull('kamar')->distinct()->pluck('kamar')->sort();

        return Inertia::render('Records/Attendance', [
            'santris' => $santrisList,
            'existingAbsensi' => $absensis,
            'activities' => $activities,
            'options' => [
                'kelas' => $availableKelas,
                'kamar' => $availableKamar
            ],
            'filters' => [
                'tanggal' => $tanggal,
                'jenis' => $jenis,
                'kelas' => $kelas,
                'kamar' => $kamar,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|string',
            'records' => 'required|array',
            'records.*.santri_id' => 'required|exists:santris,id',
            'records.*.status' => 'required|in:hadir,sakit,izin,alfa',
        ]);

        foreach ($request->records as $record) {
            Absensi::updateOrCreate(
                [
                    'santri_id' => $record['santri_id'],
                    'tanggal' => $request->tanggal,
                    'jenis' => $request->jenis,
                ],
                [
                    'status' => $record['status'],
                    'keterangan' => $record['keterangan'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Absensi berhasil disimpan.');
    }

    public function settings()
    {
        $activities = JenisAbsensi::all();
        return Inertia::render('Records/Settings/AttendanceActivities', [
            'activities' => $activities
        ]);
    }
}
