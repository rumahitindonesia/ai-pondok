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
        $stats = [
            'total_santri' => 0,
            'active_santri' => 0,
            'new_registrations' => 0,
            'monthly_revenue' => [],
            'daily_payments' => [],
        ];

        try {
            $stats['total_santri'] = \App\Models\Santri::count();
            $stats['active_santri'] = \App\Models\Santri::where('status', 'Santri Aktif')->count();
            $stats['new_registrations'] = \App\Models\PsbRegistration::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();
        } catch (\Exception $e) {
            \Log::warning('Dashboard stats query failed: ' . $e->getMessage());
        }

        try {
            $now = Carbon::now();
            $daysInMonth = $now->daysInMonth;

            $dailyData = [];
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $dateObj = $now->copy()->day($i);
                $formattedDate = $dateObj->format('d M');
                $dailyData[$formattedDate] = ['date' => $formattedDate, 'total' => 0];
            }

            $actualPayments = Pembayaran::whereMonth('tanggal_bayar', $now->month)
                ->whereYear('tanggal_bayar', $now->year)
                ->select(DB::raw('DATE(tanggal_bayar) as date'), DB::raw('SUM(jumlah_bayar) as total'))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            foreach ($actualPayments as $payment) {
                $formattedDate = Carbon::parse($payment->date)->format('d M');
                if (isset($dailyData[$formattedDate])) {
                    $dailyData[$formattedDate]['total'] = (float) $payment->total;
                }
            }

            $stats['daily_payments'] = array_values($dailyData);
        } catch (\Exception $e) {
            \Log::warning('Dashboard payments query failed: ' . $e->getMessage());
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }
}

