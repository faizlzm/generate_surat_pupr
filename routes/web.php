<?php

use App\Http\Controllers\GenerateWordController;
use App\Http\Controllers\GeneratorPageController;
use Illuminate\Support\Facades\Route;

// Halaman utama: Generator Surat Teguran Word (satu halaman, tanpa Dashboard/wizard/draft)
Route::get('/', [GeneratorPageController::class, 'index'])->name('home');

Route::post('/buat-dokumen-word', GenerateWordController::class)
    ->name('surat.word.generate');

// Persistensi konfigurasi yang dapat digunakan ulang (tanpa draft/riwayat surat)
Route::post('/generator/simpan-pengaturan', [GeneratorPageController::class, 'saveSettings'])
    ->name('generator.settings.save');
Route::post('/generator/simpan-kelompok-audit', [GeneratorPageController::class, 'saveGroups'])
    ->name('generator.groups.save');

// Rute alur lama (PDF/wizard/draft) dialihkan ke halaman utama agar tidak membingungkan
Route::redirect('/letters', '/');
Route::redirect('/letters/create', '/');
Route::redirect('/letters/{letter}/edit', '/');
Route::redirect('/letters/{letter}/preview', '/');
Route::redirect('/letters/{letter}/download', '/');
Route::redirect('/settings', '/');
