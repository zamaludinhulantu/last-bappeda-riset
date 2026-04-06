<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Models\Research;
use App\Models\Field;
use App\Models\News;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ContactInfoController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AnnouncementController;

Route::get('/', function () {
    $fieldId = request('bidang_id');
    $institution = request('institusi');
    $year = request('tahun');
    $q = trim((string) request('q'));

    $query = Research::select(['id','judul','penulis','bidang_id','institusi_id','tahun','disetujui_pada'])
        ->with(['institution:id,nama', 'field:id,nama'])
        ->where('status', 'approved')
        ->orderByDesc('disetujui_pada')
        ->orderByDesc('id');

    if ($fieldId) { $query->where('bidang_id', $fieldId); }
    if ($institution) {
        $query->whereHas('institution', function ($builder) use ($institution) {
            $builder->where('nama', 'like', '%' . $institution . '%');
        });
    }
    if ($year) { $query->where('tahun', $year); }
    if ($q !== '') {
        $query->where(function($w) use ($q) {
            $w->where('judul', 'like', "%$q%")
              ->orWhere('penulis', 'like', "%$q%");
        });
    }

    $researches = $query->paginate(10)->withQueryString();
    $highlightedResearches = (clone $query)->take(8)->get();
    $fields = Field::orderBy('nama')->get(['id','nama']);
    $newsItems = News::published()
        ->latest('dipublikasikan_pada')
        ->latest()
        ->take(3)
        ->get();

    return view('welcome', compact('researches', 'fields', 'highlightedResearches', 'newsItems'));
});

// Publik: Halaman Statis
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::get('/statistics', [PublicController::class, 'statistics'])->name('public.statistics');
Route::view('/guide', 'public.guide')->name('public.guide');
Route::get('/pengumuman', [PublicController::class, 'announcements'])->name('public.announcements');
Route::get('/berita', [PublicController::class, 'news'])->name('public.news');
Route::get('/berita/{news:slug}', [PublicController::class, 'newsShow'])->name('public.news.show');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');

    // Profil pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Penelitian (semua user login)
    Route::get('/researches', [ResearchController::class, 'index'])->name('researches.index');
    Route::get('/researches/create', [ResearchController::class, 'create'])->name('researches.create');
    Route::post('/researches', [ResearchController::class, 'store'])->name('researches.store');
    Route::get('/researches/export', [ResearchController::class, 'export'])->name('researches.export');
    Route::get('/researches/export-pdf', [ResearchController::class, 'exportPdf'])->name('researches.export.pdf');
    Route::get('/researches/{research}/edit', [ResearchController::class, 'edit'])->name('researches.edit');
    Route::put('/researches/{research}', [ResearchController::class, 'update'])->name('researches.update');
    Route::delete('/researches/{research}', [ResearchController::class, 'destroy'])->name('researches.destroy');
    Route::get('/researches/{research}', [ResearchController::class, 'show'])->name('researches.show');
     
    // Laporan
    Route::get('/reports/statistics', [ReportController::class, 'statistics'])->name('reports.statistics');

    // SPK otomatis berbasis data yang ada (tidak perlu input manual)
    Route::get('/spk/auto-rank', [SpkController::class, 'autoRank'])->name('spk.auto-rank');

    // Khusus admin
    Route::middleware('isAdmin')->group(function () {
        Route::post('/researches/{research}/approve', [ResearchController::class, 'approve'])->name('researches.approve');
        Route::post('/researches/{research}/reject', [ResearchController::class, 'reject'])->name('researches.reject');
    });

    // Verifikasi oleh Kesbangpol
    Route::post('/researches/{research}/kesbang-verify', [ResearchController::class, 'verifyKesbang'])
        ->name('researches.kesbang.verify');
    Route::post('/researches/{research}/kesbang-reject', [ResearchController::class, 'rejectKesbang'])
        ->name('researches.kesbang.reject');

    // Unggah hasil penelitian (setelah selesai)
    Route::get('/researches/{research}/results', [ResearchController::class, 'editResults'])
        ->name('researches.results.edit');
    Route::post('/researches/{research}/results', [ResearchController::class, 'uploadResults'])
        ->name('researches.results.update');
    Route::post('/researches/{research}/remind-results', [ResearchController::class, 'remindResults'])
        ->name('researches.remind-results');

    // Menu khusus: daftar penelitian saya untuk unggah hasil
    Route::get('/my/results', [ResearchController::class, 'myResults'])
        ->name('researches.results.my');
});

// Admin-only: Kelola Bidang
Route::middleware(['auth','isAdmin'])->group(function () {
    Route::get('/fields', [\App\Http\Controllers\FieldController::class, 'index'])->name('fields.index');
    Route::post('/fields', [\App\Http\Controllers\FieldController::class, 'store'])->name('fields.store');
    Route::patch('/fields/{field}', [\App\Http\Controllers\FieldController::class, 'update'])->name('fields.update');
    Route::delete('/fields/{field}', [\App\Http\Controllers\FieldController::class, 'destroy'])->name('fields.destroy');
    Route::get('/contact-info', [ContactInfoController::class, 'edit'])->name('contact-info.edit');
    Route::post('/contact-info', [ContactInfoController::class, 'update'])->name('contact-info.update');
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
    Route::post('/news', [NewsController::class, 'store'])->name('news.store');
    Route::get('/news/{news}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('/news/{news}', [NewsController::class, 'update'])->name('news.update');
    Route::delete('/news/{news}', [NewsController::class, 'destroy'])->name('news.destroy');
});

require __DIR__.'/auth.php';










// Admin routes for complete research data viewing
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/researches', [\App\Http\Controllers\Admin\ResearchAdminController::class, 'index'])
            ->name('researches.index');
        Route::get('/researches/{research}', [\App\Http\Controllers\Admin\ResearchAdminController::class, 'show'])
            ->name('researches.show');
        Route::get('/researches/{research}/download/{field}', [\App\Http\Controllers\Admin\ResearchAdminController::class, 'download'])
            ->name('researches.download');
        Route::delete('/researches/{research}/file/{field}', [\App\Http\Controllers\Admin\ResearchAdminController::class, 'destroyFile'])
            ->name('researches.file.destroy');
        Route::post('/researches/{research}/remind-results', [\App\Http\Controllers\Admin\ResearchAdminController::class, 'remindResults'])
            ->name('researches.remind-results');
    });

// Auth routes for researchers to download their own files
Route::middleware(['auth'])
    ->group(function () {
        Route::get('/researches/{research}/download/{field}', [\App\Http\Controllers\ResearchDownloadController::class, 'download'])
            ->name('researches.download');
    });

// Super Admin: kelola akun & hak akses
Route::middleware(['auth', 'superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.role.update');
        Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.password.reset');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });
