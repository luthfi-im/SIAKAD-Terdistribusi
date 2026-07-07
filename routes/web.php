<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KrsController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\BaakController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PusatController;
use Illuminate\Support\Facades\Schedule;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/', function () {
    return auth()->check() ? redirect('/krs') : redirect('/login');
});

// Halaman
Route::middleware(['auth', 'role:dosen'])->get('/dosen', function () {
    return view('dosen.dashboard', [
        'user' => auth()->user(),
        'namaRegional' => match (session('current_regional', 1)) {
            2 => 'Regional 2 · Fakultas Ekonomi dan Bisnis',
            3 => 'Regional 3 · Fakultas Kedokteran dan Ilmu Kesehatan',
            default => 'Regional 1 · Fakultas Teknik dan Ilmu Komputer',
        },
    ]);
});



Route::middleware(['auth', 'role:baak_pusat'])->get('/pusat', function () {
    return view('pusat.dashboard', ['user' => auth()->user()]);
});

Route::prefix('api')->middleware(['auth', 'role:baak_pusat'])->group(function () {
    Route::get('/pusat/ringkasan', [PusatController::class, 'ringkasan']);
    Route::get('/pusat/krs', [PusatController::class, 'monitoringKrs']);
    Route::get('/pusat/nilai', [PusatController::class, 'monitoringNilai']);

    Route::get('/pusat/mata-kuliah', [PusatController::class, 'daftarMataKuliahPerRegional']);
    Route::post('/pusat/mata-kuliah', [PusatController::class, 'storeMataKuliah']);
    Route::get('/pusat/dosen', [PusatController::class, 'daftarDosenPerRegional']);
    Route::post('/pusat/dosen', [PusatController::class, 'storeDosen']);
    Route::get('/pusat/ruangan', [PusatController::class, 'daftarRuanganPerRegional']);
    Route::post('/pusat/ruangan', [PusatController::class, 'storeRuangan']);

    Route::get('/pusat/users', [PusatController::class, 'daftarUser']); 
    Route::post('/pusat/users', [PusatController::class, 'storeUser']);

    Route::post('/pusat/sinkron', [PusatController::class, 'sinkronSekarang']);
});

Route::middleware(['auth', 'role:baak'])->get('/baak', function () {
    return view('baak.dashboard', [
        'user' => auth()->user(),
        'namaRegional' => match (session('current_regional', 1)) {
            2 => 'Regional 2 · Fakultas Ekonomi dan Bisnis',
            3 => 'Regional 3 · Fakultas Kedokteran dan Ilmu Kesehatan',
            default => 'Regional 1 · Fakultas Teknik dan Ilmu Komputer',
        },
    ]);
});

Route::middleware(['auth', 'role:baak_pusat'])->get('/pusat', function () {
    return view('pusat.dashboard', ['user' => auth()->user()]);
});

Route::middleware(['auth', 'role:mahasiswa'])->get('/krs', function () {
    return view('krs.dashboard', [
        'user' => auth()->user(),
        'namaRegional' => match (session('current_regional', 1)) {
            2 => 'Regional 2 · Fakultas Ekonomi dan Bisnis',
            3 => 'Regional 3 · Fakultas Kedokteran dan Ilmu Kesehatan',
            default => 'Regional 1 · Fakultas Teknik dan Ilmu Komputer',
        },
    ]);
});



// API internal (dipanggil dari fetch() di halaman Blade sendiri, bukan API publik)
Route::prefix('api')->group(function () {
    Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
        Route::get('/krs/dashboard', [KrsController::class, 'dashboard']);
        Route::post('/krs/ambil', [KrsController::class, 'ambilKelas']);
        Route::get('/krs/nilai', [KrsController::class, 'nilaiSaya']);
    });

    Route::middleware(['auth', 'role:dosen'])->group(function () {
        Route::get('/dosen/kelas', [DosenController::class, 'kelasSaya']);
        Route::get('/dosen/kelas/{idKelas}/peserta', [DosenController::class, 'pesertaKelas']);
        Route::post('/dosen/presensi', [DosenController::class, 'inputPresensi']);
        Route::get('/dosen/kelas/{idKelas}/rekap-presensi', [DosenController::class, 'rekapPresensi']);
        Route::post('/dosen/nilai', [DosenController::class, 'inputNilai']);
        Route::post('/dosen/kelas/{idKelas}/finalisasi', [DosenController::class, 'finalisasiNilai']);
    });

    Route::middleware(['auth', 'role:baak'])->group(function () {
        Route::post('/baak/mata-kuliah', [BaakController::class, 'storeMataKuliah']);
        Route::get('/baak/mata-kuliah', [BaakController::class, 'daftarMataKuliahRegional']);
        Route::put('/baak/mata-kuliah/{kodeMk}', [BaakController::class, 'updateMataKuliah']);
        Route::post('/baak/kalender', [BaakController::class, 'storeKalender']);
        Route::post('/baak/kalender/{id}/toggle', [BaakController::class, 'toggleKalender']);
        Route::get('/baak/kalender', [BaakController::class, 'daftarKalender']);
        Route::get('/baak/ruangan', [BaakController::class, 'daftarRuangan']);
        Route::post('/baak/ruangan', [BaakController::class, 'storeRuangan']);
        Route::post('/baak/nilai/revisi', [BaakController::class, 'revisiNilai']);
        Route::get('/baak/audit-log', [BaakController::class, 'auditLog']);
        Route::get('/baak/ekspor-pddikti', [BaakController::class, 'eksporPddikti']);
        Route::get('/baak/kelas', [BaakController::class, 'daftarKelas']);
        Route::post('/baak/kelas', [BaakController::class, 'storeKelas']);
        Route::get('/baak/kelas-lengkap', [BaakController::class, 'daftarKelasLengkap']);
        Route::get('/baak/dosen', [BaakController::class, 'daftarDosen']);
        Route::post('/baak/dosen', [BaakController::class, 'storeDosen']);
        Route::post('/baak/dosen/{nip}/toggle', [BaakController::class, 'toggleDosenAktif']);
        Route::get('/baak/status-keuangan', [BaakController::class, 'daftarStatusKeuangan']);
        Route::post('/baak/status-keuangan/toggle', [BaakController::class, 'toggleStatusKeuangan']);
    });
});

Schedule::command('sinkron:pusat')->dailyAt('01:00');
