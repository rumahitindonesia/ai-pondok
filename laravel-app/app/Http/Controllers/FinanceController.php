<?php

namespace App\Http\Controllers;

use App\Models\TagihanSpp;
use App\Models\Pembayaran;
use App\Models\Santri;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index()
    {
        $totalBilled = TagihanSpp::sum('jumlah');
        $totalPaid = Pembayaran::sum('jumlah_bayar');
        $unpaidCount = TagihanSpp::where('status', 'belum_lunas')->count();
        
        $recentPayments = Pembayaran::with('tagihan.santri')
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Finance/Index', [
            'stats' => [
                'total_billed' => $totalBilled,
                'total_paid' => $totalPaid,
                'unpaid_count' => $unpaidCount,
            ],
            'recent_payments' => $recentPayments
        ]);
    }

    public function billings()
    {
        $billings = TagihanSpp::with('santri')
            ->latest()
            ->paginate(10);

        return Inertia::render('Finance/Billing', [
            'billings' => $billings
        ]);
    }

    public function payments()
    {
        $payments = Pembayaran::with('tagihan.santri')
            ->latest()
            ->paginate(10);

        return Inertia::render('Finance/Payments', [
            'payments' => $payments
        ]);
    }

    public function createPayment(TagihanSpp $billing)
    {
        $billing->load('santri');
        return Inertia::render('Finance/CreatePayment', [
            'billing' => $billing
        ]);
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'tagihan_id' => 'required|exists:tagihan_spps,id',
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::transaction(function () use ($validated, $request) {
            if ($request->hasFile('bukti_pembayaran')) {
                $validated['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('payment_proofs', 'public');
            }

            Pembayaran::create($validated);

            // Update billing status
            $billing = TagihanSpp::findOrFail($validated['tagihan_id']);
            $totalPaid = $billing->pembayarans()->sum('jumlah_bayar');
            
            if ($totalPaid >= $billing->jumlah) {
                $billing->update(['status' => 'lunas']);
            }
        });

        return redirect()->route('finance.billings')->with('success', 'Pembayaran berhasil dicatat');
    }

    public function generateMonthlyBills(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
            'jumlah' => 'required|numeric|min:0',
            'jatuh_tempo' => 'required|date',
        ]);

        $activeSantris = Santri::where('status', 'aktif')->get();
        $count = 0;

        foreach ($activeSantris as $santri) {
            // Check if bill already exists
            $exists = TagihanSpp::where([
                'santri_id' => $santri->id,
                'bulan' => $validated['bulan'],
                'tahun' => $validated['tahun'],
            ])->exists();

            if (!$exists) {
                TagihanSpp::create([
                    'santri_id' => $santri->id,
                    'bulan' => $validated['bulan'],
                    'tahun' => $validated['tahun'],
                    'jumlah' => $validated['jumlah'],
                    'jatuh_tempo' => $validated['jatuh_tempo'],
                    'status' => 'belum_lunas',
                ]);
                $count++;
            }
        }

        return redirect()->route('finance.billings')->with('success', "$count tagihan berhasil dibuat");
    }

    public function createBilling()
    {
        $santris = Santri::where('status', 'aktif')->get();
        return Inertia::render('Finance/CreateBilling', [
            'santris' => $santris
        ]);
    }

    public function storeBilling(Request $request)
    {
        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
            'jumlah' => 'required|numeric|min:0',
            'jatuh_tempo' => 'required|date',
        ]);

        // Check for duplicate
        $exists = TagihanSpp::where([
            'santri_id' => $validated['santri_id'],
            'bulan' => $validated['bulan'],
            'tahun' => $validated['tahun'],
        ])->exists();

        if ($exists) {
            return back()->withErrors(['santri_id' => 'Tagihan untuk periode ini sudah ada untuk santri tersebut.']);
        }

        TagihanSpp::create($validated + ['status' => 'belum_lunas']);

        return redirect()->route('finance.billings')->with('success', 'Tagihan berhasil dibuat secara manual.');
    }
}
