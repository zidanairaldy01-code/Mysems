<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\ArsipController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Peserta\DashboardController as PesertaDashboardController;
use App\Http\Controllers\Admin\KlasemenController;
use App\Http\Controllers\Admin\PanitiaController;
use App\Http\Controllers\Admin\PesertaController;
use App\Http\Controllers\Admin\RegistrationManagementController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CertificateController;

/**
 * --------------------------------------------------------------------------
 * Rute Publik (Halaman Peserta / Akses Tanpa Login)
 * --------------------------------------------------------------------------
 */
Route::get('/', [PublicController::class, 'index'])->name('public.index');



// Event Publik (Hanya melihat event yang disetujui)
Route::get('/event', [EventController::class, 'publicIndex'])->name('public.event.index');
Route::get('/event/detail/{uuid}', [EventController::class, 'show'])->name('public.event.show');

// Klasemen Publik (Read-only)
Route::get('/klasemen', [KlasemenController::class, 'publicIndex'])->name('public.klasemen.index');

// Pendaftaran Event
Route::get('/event/daftar/{event_uuid}', [\App\Http\Controllers\RegistrationController::class, 'create'])->name('public.event.register');
Route::get('/event/daftar/cek/{id}', [\App\Http\Controllers\RegistrationController::class, 'check'])->name('public.event.check');
Route::post('/event/daftar/store', [\App\Http\Controllers\RegistrationController::class, 'store'])->name('public.registration.store');
Route::get('/event/daftar/sukses/{id}', [\App\Http\Controllers\RegistrationController::class, 'success'])->name('public.registration.success');

// Rute penyajian gambar secara aman (di luar auth middleware agar bisa diakses public/guest dengan proteksi referrer)
Route::get('/uploads/{directory}/{filename}', [\App\Http\Controllers\SecureImageController::class, 'serveUploads'])
    ->where('directory', 'events|profile');

Route::get('/public-image/{directory}/{filename}', [App\Http\Controllers\SecureImageController::class, 'servePublicImage'])->name('public.image');

/**
 * --------------------------------------------------------------------------
 * Rute Autentikasi (Login & Logout)
 * --------------------------------------------------------------------------
 */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/**
 * --------------------------------------------------------------------------
 * Rute Khusus Admin (Role: 1)
 * --------------------------------------------------------------------------
 */
Route::middleware(['auth', 'role:1'])->prefix('admin')->group(function () {
    
    // Halaman Utama Admin
    Route::get('/index', [DashboardController::class, 'index'])->name('admin.index');

    // Kelola Event (Approval System)
    Route::get('/event', [EventController::class, 'index'])->name('admin.event.index');
    Route::get('/event/detail/{uuid}', [EventController::class, 'showAdmin'])->name('admin.event.show');
    Route::get('/event/create', [EventController::class, 'create'])->name('admin.event.create');
    Route::get('/event/create-umum', [EventController::class, 'createUmum'])->name('admin.event.create_umum');
    Route::post('/event/create', [EventController::class, 'store'])->name('admin.event.store');
    Route::get('/event/{uuid}/edit', [EventController::class, 'edit'])->name('admin.event.edit');
    Route::put('/event/{uuid}', [EventController::class, 'update'])->name('admin.event.update');
    Route::delete('/event/{uuid}', [EventController::class, 'destroy'])->name('admin.event.destroy');
    Route::get('/event/persetujuan', [EventController::class, 'persetujuan'])->name('admin.event.persetujuan');
    Route::post('/event/approve/{uuid}', [EventController::class, 'approve'])->name('admin.event.approve');
    Route::post('/event/reject/{uuid}', [EventController::class, 'reject'])->name('admin.event.reject');
    Route::post('/event/finish/{uuid}', [EventController::class, 'finish'])->name('admin.event.finish');

    Route::get('/arsip', [ArsipController::class, 'index'])->name('admin.arsip.index');
    Route::post('/arsip/restore/{uuid}', [ArsipController::class, 'restore'])->name('admin.arsip.restore');
    Route::post('/arsip/delete/{uuid}', [ArsipController::class, 'delete'])->name('admin.arsip.destroy');
    Route::get('/arsip/detail/{uuid}', [ArsipController::class, 'show'])->name('admin.arsip.show');

    Route::get('/admin/event/status-counts', [EventController::class, 'getStatusCounts'])->name('admin.event.status-counts');

    // Arsip Event
    // Kelola Panitia
    Route::get('/panitia', [PanitiaController::class, 'index'])->name('admin.panitia.index');
    Route::get('/panitia/create', [PanitiaController::class, 'create'])->name('admin.panitia.create');
    Route::post('/panitia/create', [PanitiaController::class, 'store'])->name('admin.panitia.store');
    Route::get('/panitia/edit/{id}', [PanitiaController::class, 'edit'])->name('admin.panitia.edit');
    Route::put('/panitia/update/{id}', [PanitiaController::class, 'update'])->name('admin.panitia.update');
    Route::delete('/panitia/{id}', [PanitiaController::class, 'destroy'])->name('admin.panitia.destroy');

    // Laporan Statistik
    Route::get('/laporan', [LaporanController::class, 'index'])->name('admin.laporan.index');
    Route::get('/laporan/export-data', [LaporanController::class, 'exportData'])->name('admin.laporan.export_data');

    // Klasemen & Bracket
    Route::get('/klasemen', [KlasemenController::class, 'index'])->name('admin.klasemen.index');
    Route::post('/klasemen', [KlasemenController::class, 'update'])->name('admin.klasemen.update');
    Route::post('/klasemen/reset', [KlasemenController::class, 'reset'])->name('admin.klasemen.reset');

    // Sertifikat
    Route::get('/certificate/generate/{eventId}', [CertificateController::class, 'generate'])->name('admin.certificate.generate');

    // Kelola Tim Peserta (Pendaftaran Event)
    Route::get('/registration', [RegistrationManagementController::class, 'index'])->name('admin.registration.index');
    Route::delete('/registration/{id}', [RegistrationManagementController::class, 'destroy'])->name('admin.registration.destroy');
    Route::get('/registration/scan', [RegistrationManagementController::class, 'scan'])->name('admin.registration.scan');
    Route::post('/registration/scan/process', [RegistrationManagementController::class, 'processScan'])->name('admin.registration.scan.process');

    // Kelola Akun Peserta (Data User)
    Route::get('/peserta', [PesertaController::class, 'index'])->name('admin.peserta.index');
    Route::get('/peserta/{id}', [PesertaController::class, 'show'])->name('admin.peserta.show');
    Route::delete('/peserta/{id}', [PesertaController::class, 'destroy'])->name('admin.peserta.destroy');

    // Profil Admin
    Route::get('/profile', function () { return view('admin.profile.index'); })->name('admin.profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::post('/profile/delete-foto', [ProfileController::class, 'deleteFoto'])->name('admin.profile.delete-foto');

    // Notifikasi Admin
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::delete('/notifications/delete-all', [\App\Http\Controllers\NotificationController::class, 'deleteAll'])->name('admin.notifications.deleteAll');
});

// Rute Notifikasi Umum (Hanya butuh login)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications/read/{id}', [\App\Http\Controllers\NotificationController::class, 'read'])->name('notifications.read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

});

/**
 * --------------------------------------------------------------------------
 * Rute Khusus Panitia (Role: 0)
 * --------------------------------------------------------------------------
 */
Route::middleware(['auth', 'role:0'])->prefix('panitia')->group(function () {
    // Dashboard Panitia
    Route::get('/index', [DashboardController::class, 'index'])->name('panitia.index');

    // Kelola Event
    Route::get('/event', [EventController::class, 'index'])->name('panitia.event.index');
    Route::get('/event/detail/{uuid}', [EventController::class, 'showPanitia'])->name('panitia.event.show');
    Route::get('/event/create', [EventController::class, 'create'])->name('panitia.event.create');
    Route::get('/event/create-umum', [EventController::class, 'createUmum'])->name('panitia.event.create_umum');
    Route::post('/event/create', [EventController::class, 'store'])->name('panitia.event.store');
    Route::get('/event/{uuid}/edit', [EventController::class, 'edit'])->name('panitia.event.edit');
    Route::put('/event/{uuid}', [EventController::class, 'update'])->name('panitia.event.update');
    Route::post('/event/finish/{uuid}', [EventController::class, 'finish'])->name('panitia.event.finish');

    // Arsip Event1

    // Laporan Event
    Route::get('/laporan', [LaporanController::class, 'index'])->name('panitia.laporan.index');
    Route::get('/laporan/export-data', [LaporanController::class, 'exportData'])->name('panitia.laporan.export_data');

    // Klasemen & Bracket
    Route::get('/klasemen', [KlasemenController::class, 'index'])->name('panitia.klasemen.index');
    Route::post('/klasemen', [KlasemenController::class, 'update'])->name('panitia.klasemen.update');
    Route::post('/klasemen/reset', [KlasemenController::class, 'reset'])->name('panitia.klasemen.reset');

    // Sertifikat
    Route::get('/certificate/generate/{eventId}', [CertificateController::class, 'generate'])->name('panitia.certificate.generate');

    // Kelola Tim Peserta
    Route::get('/registration', [RegistrationManagementController::class, 'index'])->name('panitia.registration.index');
    Route::get('/registration/scan', [RegistrationManagementController::class, 'scan'])->name('panitia.registration.scan');
    Route::post('/registration/scan/process', [RegistrationManagementController::class, 'processScan'])->name('panitia.registration.scan.process');

    // Data Akun Peserta
    Route::get('/peserta', [PesertaController::class, 'index'])->name('panitia.peserta.index');
    Route::get('/peserta/{id}', [PesertaController::class, 'show'])->name('panitia.peserta.show');

    // Profil Panitia
    Route::get('/profile', function () { return view('panitia.profile.index'); })->name('panitia.profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('panitia.profile.update');
    Route::post('/profile/delete-foto', [ProfileController::class, 'deleteFoto'])->name('panitia.profile.delete-foto');

    // Notifikasi Panitia
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('panitia.notifications.index');
    Route::delete('/notifications/delete-all', [\App\Http\Controllers\NotificationController::class, 'deleteAll'])->name('panitia.notifications.deleteAll');
});

/**
 * --------------------------------------------------------------------------
 * Rute Khusus Peserta (Role: 2)
 * --------------------------------------------------------------------------
 */
Route::middleware(['auth', 'role:2'])->prefix('peserta')->group(function () {
    // Dashboard Peserta
    Route::get('/index', [PesertaDashboardController::class, 'index'])->name('peserta.index');

    // Event Sekolah (Hanya melihat event yang disetujui)
    Route::get('/event', [EventController::class, 'publicIndex'])->name('peserta.event.index');

    // Klasemen
    Route::get('/klasemen', [KlasemenController::class, 'publicIndex'])->name('peserta.klasemen.index');

    // Profil Peserta
    Route::get('/profile', function () { return view('peserta.profile.index'); })->name('peserta.profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('peserta.profile.update');

    // Notifikasi Peserta
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('peserta.notifications.index');
    Route::delete('/notifications/delete-all', [\App\Http\Controllers\NotificationController::class, 'deleteAll'])->name('peserta.notifications.deleteAll');
});