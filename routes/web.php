<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;

// Halaman Utama: Menampilkan daftar kategori form
Route::get('/', [FormController::class, 'index'])->name('home');

// Halaman Form: Menampilkan form spesifik berdasarkan kode (misal: KKH-01-BANDARA)
Route::get('/form/{form_code}', [FormController::class, 'show'])->name('form.show');

// Proses Submit Form
Route::post('/form/{form_code}', [FormController::class, 'store'])->name('form.store');

// Export PDF
Route::get('/form/export/{id}', [FormController::class, 'exportPdf'])->name('form.pdf');

// Hapus Data Pengawasan
Route::delete('/form/delete/{id}', [FormController::class, 'destroy'])->name('form.destroy');