<?php

namespace App\Http\Controllers;

use App\Models\TagihanSpp;
use App\Models\Pembayaran;
use App\Models\Santri;
use App\Models\JenisPembayaran;
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

    public function billings(Request $request)
    {
        $query = TagihanSpp::with(['santri', 'jenisPembayaran'])->latest();

        if ($request->filled('jenis')) {
            $query->where('jenis_pembayaran_id', $request->input('jenis'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $billings = $query->paginate(15)->withQueryString();

        return Inertia::render('Finance/Billing', [
            'billings' => $billings,
            'jenisPembayarans' => JenisPembayaran::orderBy('nama')->get(),
            'entitasOptions' => Santri::whereNotNull('entitas')->where('entitas', '!=', '')->distinct()->pluck('entitas'),
            'statusOptions' => [
                'Santri Aktif', 
                'Santri Keluar', 
                'Santri Lulus - Alumni', 
                'Alumni Aktif - dipondok', 
                'Alumni Tidak Aktif - diluar pondok', 
                'Santri Magang', 
                'Alumni Magang'
            ],
            'filters' => $request->only(['jenis', 'status']),
        ]);
    }

    public function payments()
    {
        $payments = Pembayaran::with('tagihan.santri')
            ->latest()
            ->paginate(15);

        return Inertia::render('Finance/Payments', [
            'payments' => $payments
        ]);
    }

    public function createPayment(TagihanSpp $billing)
    {
        $billing->load(['santri', 'jenisPembayaran']);
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
            'jenis_pembayaran_id' => 'nullable|exists:jenis_pembayarans,id',
            'entitas' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $jenisSpp = JenisPembayaran::where('kode', 'spp')->first();

        $query = Santri::query();
        
        if ($request->filled('entitas')) {
            $query->where('entitas', $validated['entitas']);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $validated['status']);
        } else {
            // Default to 'Santri Aktif' if no status specified to prevent accidental generation for alumni
            $query->where('status', 'Santri Aktif');
        }

        $targetSantris = $query->get();
        $count = 0;

        foreach ($targetSantris as $santri) {
            $exists = TagihanSpp::where([
                'santri_id' => $santri->id,
                'bulan' => $validated['bulan'],
                'tahun' => $validated['tahun'],
                'jenis_pembayaran_id' => $validated['jenis_pembayaran_id'] ?? $jenisSpp?->id,
            ])->exists();

            if (!$exists) {
                TagihanSpp::create([
                    'santri_id' => $santri->id,
                    'jenis_pembayaran_id' => $validated['jenis_pembayaran_id'] ?? $jenisSpp?->id,
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
        $santris = Santri::whereIn('status', ['Santri Aktif', 'Santri Magang', 'Alumni Aktif - dipondok'])->get();
        $jenisPembayarans = JenisPembayaran::orderBy('nama')->get();
        return Inertia::render('Finance/CreateBilling', [
            'santris' => $santris,
            'jenisPembayarans' => $jenisPembayarans,
            'entitasOptions' => Santri::whereNotNull('entitas')->where('entitas', '!=', '')->distinct()->pluck('entitas'),
        ]);
    }

    public function storeBilling(Request $request)
    {
        $jenisPembayaran = JenisPembayaran::find($request->input('jenis_pembayaran_id'));

        $rules = [
            'santri_id' => 'required|exists:santris,id',
            'jenis_pembayaran_id' => 'required|exists:jenis_pembayarans,id',
            'jumlah' => 'required|numeric|min:0',
            'jatuh_tempo' => 'required|date',
            'keterangan' => 'nullable|string',
        ];

        // bulan & tahun only required for bulanan types
        if ($jenisPembayaran && $jenisPembayaran->sifat === 'bulanan') {
            $rules['bulan'] = 'required|string';
            $rules['tahun'] = 'required|integer';
        }

        $validated = $request->validate($rules);

        // For bulanan: check duplicate per period
        if ($jenisPembayaran && $jenisPembayaran->sifat === 'bulanan') {
            $exists = TagihanSpp::where([
                'santri_id' => $validated['santri_id'],
                'jenis_pembayaran_id' => $validated['jenis_pembayaran_id'],
                'bulan' => $validated['bulan'],
                'tahun' => $validated['tahun'],
            ])->exists();

            if ($exists) {
                return back()->withErrors(['bulan' => 'Tagihan periode ini sudah ada untuk santri tersebut.']);
            }
        }

        TagihanSpp::create($validated + ['status' => 'belum_lunas']);

        return redirect()->route('finance.billings')->with('success', 'Tagihan berhasil dibuat.');
    }
}
