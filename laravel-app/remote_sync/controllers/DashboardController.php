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
        // Try to fetch data from Python engine
        try {
            $response = Http::get('http://localhost:8001/api/user-stats');
            $stats = $response->json();
        } catch (\Exception $e) {
            $stats = [
                'total_santri' => 0,
                'active_santri' => 0,
                'new_registrations' => 0,
                'monthly_revenue' => [],
                'error' => 'Python engine not reachable'
            ];
        }

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
