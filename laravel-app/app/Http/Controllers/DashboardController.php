<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch real stats directly from the database
        $stats = [
            'total_santri' => \App\Models\Santri::count(),
            'active_santri' => \App\Models\Santri::where('status', 'Santri Aktif')->count(),
            'new_registrations' => \App\Models\PsbRegistration::whereMonth('created_at', Carbon::now()->month)
                                    ->whereYear('created_at', Carbon::now()->year)
                                    ->count(),
            'monthly_revenue' => [], // Keeping for structure, filled dynamically later if needed
        ];

        // Add daily payments for current month
        $now = Carbon::now();
        $daysInMonth = $now->daysInMonth;
        
        // 1. Create a base array with all days of the month defaulting to 0
        $dailyData = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dateObj = clone $now;
            $dateObj->day = $i;
            $formattedDate = $dateObj->format('d M');
            $dailyData[$formattedDate] = [
                'date' => $formattedDate,
                'total' => 0
            ];
        }

        // 2. Fetch actual payment data from the database
        $actualPayments = Pembayaran::whereMonth('tanggal_bayar', $now->month)
            ->whereYear('tanggal_bayar', $now->year)
            ->select(DB::raw('DATE(tanggal_bayar) as date'), DB::raw('SUM(jumlah_bayar) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 3. Overlay the actual totals onto the base array
        foreach ($actualPayments as $payment) {
            $formattedDate = Carbon::parse($payment->date)->format('d M');
            if (isset($dailyData[$formattedDate])) {
                $dailyData[$formattedDate]['total'] = (float) $payment->total;
            }
        }

        $stats['daily_payments'] = array_values($dailyData);

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }
}
