<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\PSBController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\JenisAbsensiController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\CapaianSantriController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\PengurusController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public PSB Routes
Route::get('/register-psb', [PSBController::class, 'register'])->name('psb.register');
Route::post('/register-psb', [PSBController::class, 'store'])->name('psb.store');
Route::get('/register-psb/success', [PSBController::class, 'success'])->name('psb.success');

// Public Santri Profile
Route::get('/s/{nis}', [\App\Http\Controllers\PublicProfileController::class, 'show'])->name('public.santri.profile');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Data Santri
    Route::middleware(['can:view santri'])->group(function () {
        Route::get('/santri/settings', [SantriController::class, 'settings'])->name('santri.settings');
        Route::post('/santri/bulk-status', [SantriController::class, 'bulkUpdateStatus'])->name('santri.bulk-status');
        Route::resource('santri', SantriController::class);

        // Santri Attribute Management
        Route::post('/admin/kelas', [KelasController::class, 'store'])->name('admin.kelas.store');
        Route::delete('/admin/kelas/{kelas}', [KelasController::class, 'destroy'])->name('admin.kelas.destroy');
        Route::post('/admin/kamars', [KamarController::class, 'store'])->name('admin.kamars.store');
        Route::delete('/admin/kamars/{kamar}', [KamarController::class, 'destroy'])->name('admin.kamars.destroy');

        // Achievement & Portfolio Management (tied to Santri)
        Route::post('/santri/{santri}/achievements', [CapaianSantriController::class, 'store'])->name('santri.achievements.store');
        Route::delete('/achievements/{achievement}', [CapaianSantriController::class, 'destroy'])->name('achievements.destroy');
        
        Route::post('/santri/{santri}/portfolios', [PortfolioController::class, 'store'])->name('santri.portfolios.store');
        Route::put('/portfolios/{portfolio}', [PortfolioController::class, 'update'])->name('portfolios.update');
        Route::delete('/portfolios/{portfolio}', [PortfolioController::class, 'destroy'])->name('portfolios.destroy');
    });

    // Struktur Organisasi (Pengurus)
    Route::middleware(['can:view organisasi'])->prefix('organisasi')->name('organisasi.')->group(function () {
        Route::resource('jabatan', JabatanController::class)->except(['create', 'show', 'edit']);
        Route::resource('pengurus', PengurusController::class)->except(['create', 'show', 'edit']);
    });
    
    // Admin PSB Management
    Route::middleware(['can:view psb'])->group(function () {
        Route::get('/admin/psb', [PSBController::class, 'adminIndex'])->name('admin.psb.index');
        Route::patch('/admin/psb/{psb}/status', [PSBController::class, 'updateStatus'])->name('admin.psb.status');
    });

    // Finance Routes
    Route::middleware(['can:view finance'])->group(function () {
        Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::get('/finance/billings', [FinanceController::class, 'billings'])->name('finance.billings');
        Route::get('/finance/payments', [FinanceController::class, 'payments'])->name('finance.payments');
        Route::get('/finance/payments/create/{billing}', [FinanceController::class, 'createPayment'])->name('finance.payments.create');
        Route::post('/finance/payments', [FinanceController::class, 'storePayment'])->name('finance.payments.store');
        Route::post('/finance/billings/generate', [FinanceController::class, 'generateMonthlyBills'])->name('finance.billings.generate');
        Route::get('/finance/billings/create', [FinanceController::class, 'createBilling'])->name('finance.billings.create');
        Route::post('/finance/billings', [FinanceController::class, 'storeBilling'])->name('finance.billings.store');
        Route::get('/finance/settings', [JenisPembayaranController::class, 'index'])->name('finance.settings');
        Route::post('/finance/settings/jenis', [JenisPembayaranController::class, 'store'])->name('finance.jenis.store');
        Route::patch('/finance/settings/jenis/{jenisPembayaran}', [JenisPembayaranController::class, 'update'])->name('finance.jenis.update');
        Route::delete('/finance/settings/jenis/{jenisPembayaran}', [JenisPembayaranController::class, 'destroy'])->name('finance.jenis.destroy');
    });

    // Attendance & Discipline Routes
    Route::middleware(['can:view attendance'])->group(function () {
        Route::get('/records/attendance', [AbsensiController::class, 'index'])->name('records.attendance');
        Route::post('/records/attendance', [AbsensiController::class, 'store'])->name('records.attendance.store');
        Route::get('/records/attendance/settings', [AbsensiController::class, 'settings'])->name('records.attendance.settings');
        Route::post('/records/attendance/types', [JenisAbsensiController::class, 'store'])->name('records.attendance.types.store');
        Route::delete('/records/attendance/types/{jenisAbsensi}', [JenisAbsensiController::class, 'destroy'])->name('records.attendance.types.destroy');
    });
    
    Route::middleware(['can:view discipline'])->group(function () {
        Route::get('/records/discipline', [PelanggaranController::class, 'index'])->name('records.discipline');
        Route::get('/records/discipline/create', [PelanggaranController::class, 'create'])->name('records.discipline.create');
        Route::post('/records/discipline', [PelanggaranController::class, 'store'])->name('records.discipline.store');
        Route::delete('/records/discipline/{pelanggaran}', [PelanggaranController::class, 'destroy'])->name('records.discipline.destroy');
    });

    // Curriculum & Learning Routes
    Route::middleware(['can:view materi'])->group(function () {
        Route::resource('materi', MateriController::class);
    });
    
    // User & Role Management
    Route::middleware(['can:view users'])->group(function () {
        Route::get('/admin/users', [\App\Http\Controllers\UserController::class, 'index'])->name('admin.users.index');
        Route::post('/admin/users', [\App\Http\Controllers\UserController::class, 'store'])->name('admin.users.store');
        Route::patch('/admin/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('admin.users.destroy');
        
        Route::get('/admin/roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('admin.roles.index');
        Route::post('/admin/roles', [\App\Http\Controllers\RoleController::class, 'store'])->name('admin.roles.store');
        Route::patch('/admin/roles/{role}', [\App\Http\Controllers\RoleController::class, 'update'])->name('admin.roles.update');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
