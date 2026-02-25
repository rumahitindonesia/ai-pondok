<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicProfileController extends Controller
{
    /**
     * Display the public profile of a Santri by NIS.
     *
     * @param  string  $nis
     * @return \Inertia\Response
     */
    public function show($nis)
    {
        // Fetch Santri and eager load relationships
        $santri = Santri::with([
            'wali',
            'absensis.jenisAbsensi', // Load the activity type name
            'pelanggarans',
            'kelas',
            'kamar',
            'tagihanSpps' => function($query) {
                // Order bills by latest
                $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc');
            },
            'tagihanSpps.pembayarans', // Load payments for these bills
            'achievements.materi',
            'portfolios'
        ])->where('nis', $nis)->firstOrFail();

        // Calculate summary statistics
        // 1. Attendance points (just an example calculation)
        $totalHadir = $santri->absensis->where('status', 'hadir')->count();
        $totalAbsen = $santri->absensis->count();
        $attendanceRate = $totalAbsen > 0 ? round(($totalHadir / $totalAbsen) * 100) : 0;

        // 2. Discipline points
        $totalPelanggaran = $santri->pelanggarans->sum('poin');

        // Prepare the mapped data for the frontend
        return Inertia::render('Public/SantriProfile', [
            'santri' => $santri,
            'stats' => [
                'attendanceRate' => $attendanceRate,
                'totalPelanggaran' => $totalPelanggaran,
            ]
        ]);
    }
}
