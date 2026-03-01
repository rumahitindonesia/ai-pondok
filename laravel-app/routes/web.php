<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\PSBController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\JenisAbsensiController;
use App\Http\Controllers\JenisPembayaranController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\CapaianSantriController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\PengurusController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public PSB Routes
Route::get('/register-psb', [PSBController::class, 'register'])->name('psb.register');
Route::post('/register-psb', [PSBController::class, 'store'])->name('psb.store');
Route::get('/register-psb/success', [PSBController::class, 'success'])->name('psb.success');

// Tahap 2 Lengkapi Data
Route::get('/psb/lengkapi-data/{token}', [PSBController::class, 'editLengkap'])->name('psb.lengkapi_data');
Route::post('/psb/lengkapi-data/{token}', [PSBController::class, 'updateLengkap'])->name('psb.update_lengkapi_data');

// Public Santri Profile
Route::get('/s/{nis}', [\App\Http\Controllers\PublicProfileController::class, 'show'])->name('public.santri.profile');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'sections' => \App\Models\HomepageSection::where('is_active', true)->get()->keyBy('section_key'),
        'featuredSantris' => \App\Models\Santri::where('is_portfolio_featured', true)
            ->where('status', '!=', 'Santri Keluar')
            ->select('id', 'nis', 'nama', 'foto', 'bio', 'cita_cita', 'entitas', 'angkatan')
            ->inRandomOrder()
            ->limit(4)
            ->get()
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Data Santri
    Route::middleware(['can:view santri'])->group(function () {
        Route::get('/santri/settings', [SantriController::class, 'settings'])->name('santri.settings');
        Route::get('/santri', [SantriController::class, 'index'])->name('santri.index');
        Route::get('/santri/{santri}', [SantriController::class, 'show'])->name('santri.show');

        Route::middleware(['can:create santri'])->group(function () {
            Route::get('/santri/create', [SantriController::class, 'create'])->name('santri.create');
            Route::post('/santri', [SantriController::class, 'store'])->name('santri.store');
            Route::post('/admin/kelas', [KelasController::class, 'store'])->name('admin.kelas.store');
            Route::post('/admin/kamars', [KamarController::class, 'store'])->name('admin.kamars.store');
            Route::post('/santri/{santri}/achievements', [CapaianSantriController::class, 'store'])->name('santri.achievements.store');
            Route::post('/santri/{santri}/portfolios', [PortfolioController::class, 'store'])->name('santri.portfolios.store');
        });

        Route::middleware(['can:update santri'])->group(function () {
            Route::get('/santri/{santri}/edit', [SantriController::class, 'edit'])->name('santri.edit');
            Route::put('/santri/{santri}', [SantriController::class, 'update'])->name('santri.update');
            Route::patch('/santri/{santri}', [SantriController::class, 'update']);
            Route::post('/santri/bulk-status', [SantriController::class, 'bulkUpdateStatus'])->name('santri.bulk-status');
            Route::put('/portfolios/{portfolio}', [PortfolioController::class, 'update'])->name('portfolios.update');
        });

        Route::middleware(['can:delete santri'])->group(function () {
            Route::delete('/santri/{santri}', [SantriController::class, 'destroy'])->name('santri.destroy');
            Route::delete('/admin/kelas/{kelas}', [KelasController::class, 'destroy'])->name('admin.kelas.destroy');
            Route::delete('/admin/kamars/{kamar}', [KamarController::class, 'destroy'])->name('admin.kamars.destroy');
            Route::delete('/achievements/{achievement}', [CapaianSantriController::class, 'destroy'])->name('achievements.destroy');
            Route::delete('/portfolios/{portfolio}', [PortfolioController::class, 'destroy'])->name('portfolios.destroy');
        });
    });

    // Struktur Organisasi (Pengurus)
    Route::middleware(['can:view organisasi'])->prefix('organisasi')->name('organisasi.')->group(function () {
        Route::resource('jabatan', JabatanController::class)->except(['create', 'show', 'edit']);
        Route::resource('pengurus', PengurusController::class)->except(['create', 'show', 'edit']);
    });
    
    // Admin PSB Management
    Route::middleware(['can:view psb'])->group(function () {
        Route::get('/admin/psb', [PSBController::class, 'adminIndex'])->name('admin.psb.index');
        
        Route::middleware(['can:update psb'])->group(function () {
            Route::patch('/admin/psb/{psb}/status', [PSBController::class, 'updateStatus'])->name('admin.psb.status');
        });
        
        // Form Builder (Dynamic PSB Stage 2)
        Route::get('/admin/psb/form-builder', [\App\Http\Controllers\Admin\FormBuilderController::class, 'index'])->name('admin.psb.form-builder');
        
        Route::middleware(['can:create psb'])->group(function () {
            Route::post('/admin/psb/form-builder', [\App\Http\Controllers\Admin\FormBuilderController::class, 'store'])->name('admin.psb.form-builder.store');
            Route::post('/admin/psb/form-builder/reorder', [\App\Http\Controllers\Admin\FormBuilderController::class, 'reorder'])->name('admin.psb.form-builder.reorder');
        });
        
        Route::middleware(['can:update psb'])->group(function () {
            Route::put('/admin/psb/form-builder/{question}', [\App\Http\Controllers\Admin\FormBuilderController::class, 'update'])->name('admin.psb.form-builder.update');
        });
        
        Route::middleware(['can:delete psb'])->group(function () {
            Route::delete('/admin/psb/form-builder/{question}', [\App\Http\Controllers\Admin\FormBuilderController::class, 'destroy'])->name('admin.psb.form-builder.destroy');
        });
    });

    // Homepage Management
    Route::middleware(['can:view homepage'])->group(function () {
        Route::get('/admin/homepage', [\App\Http\Controllers\Admin\HomepageController::class, 'index'])->name('admin.homepage.index');
        
        Route::middleware(['can:update homepage'])->group(function () {
            Route::put('/admin/homepage/{section}', [\App\Http\Controllers\Admin\HomepageController::class, 'update'])->name('admin.homepage.update');
        });
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
        Route::get('/admin/roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('admin.roles.index');

        Route::middleware(['can:create users'])->group(function () {
            Route::post('/admin/users', [\App\Http\Controllers\UserController::class, 'store'])->name('admin.users.store');
            Route::post('/admin/roles', [\App\Http\Controllers\RoleController::class, 'store'])->name('admin.roles.store');
        });

        Route::middleware(['can:update users'])->group(function () {
            Route::patch('/admin/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('admin.users.update');
            Route::patch('/admin/roles/{role}', [\App\Http\Controllers\RoleController::class, 'update'])->name('admin.roles.update');
        });

        Route::middleware(['can:delete users'])->group(function () {
            Route::delete('/admin/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('admin.users.destroy');
        });
    });

    // --- CONTENT ASSIGNMENT MODULE ---
    // User Routes
    Route::middleware(['can:view content request'])->group(function () {
        Route::get('/content-requests/my', [\App\Http\Controllers\ContentRequestController::class, 'myRequests'])->name('content-requests.my');
        
        Route::middleware(['can:create content request'])->group(function () {
            Route::get('/content-requests/create', [\App\Http\Controllers\ContentRequestController::class, 'create'])->name('content-requests.create');
            Route::post('/content-requests', [\App\Http\Controllers\ContentRequestController::class, 'store'])->name('content-requests.store');
            Route::put('/content-requests/{contentRequest}/metrics', [\App\Http\Controllers\ContentRequestController::class, 'updateMetrics'])->name('content-requests.metrics');
        });
    });

    // Admin / Manager Routes
    Route::middleware(['can:view content manager'])->group(function () {
        // Social Media Integration
        Route::get('/social/instagram/redirect', [\App\Http\Controllers\SocialAccountController::class, 'redirectToInstagram'])->name('social.instagram.redirect');
        Route::get('/social/instagram/callback', [\App\Http\Controllers\SocialAccountController::class, 'handleInstagramCallback'])->name('social.instagram.callback');
        Route::delete('/social/instagram/disconnect', [\App\Http\Controllers\SocialAccountController::class, 'disconnect'])->name('social.instagram.disconnect');
        Route::post('/admin/content-manager/{contentRequest}/sync', [\App\Http\Controllers\SocialAccountController::class, 'syncMediaInsights'])->name('admin.social.instagram.sync');

        Route::get('/admin/content-manager', [\App\Http\Controllers\Admin\ContentManagerController::class, 'index'])->name('admin.content.manager.index');
        Route::get('/admin/content-analytics', [\App\Http\Controllers\Admin\ContentAnalyticsController::class, 'index'])->name('admin.content.analytics.index');
        Route::get('/admin/content-analytics/ai-insight', [\App\Http\Controllers\Admin\ContentAnalyticsController::class, 'getAiInsight'])->name('admin.content.analytics.ai');
        Route::put('/admin/content-manager/{contentRequest}/assign', [\App\Http\Controllers\Admin\ContentManagerController::class, 'assign'])->name('admin.content.manager.assign');
        Route::put('/admin/content-manager/{contentRequest}/status', [\App\Http\Controllers\Admin\ContentManagerController::class, 'updateStatus'])->name('admin.content.manager.status');
        Route::put('/admin/content-manager/{contentRequest}/metrics', [\App\Http\Controllers\Admin\ContentManagerController::class, 'updateMetrics'])->name('admin.content.manager.metrics');
    });

    // Staff / Executor Routes
    Route::middleware(['can:view content staff'])->group(function () {
        Route::get('/media-staff/tasks', [\App\Http\Controllers\MediaStaff\ContentTaskController::class, 'index'])->name('media.tasks.index');
        Route::put('/media-staff/tasks/{contentTask}/status', [\App\Http\Controllers\MediaStaff\ContentTaskController::class, 'updateStatus'])->name('media.tasks.status');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
