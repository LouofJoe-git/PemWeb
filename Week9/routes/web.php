<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Cepatik Cloud Printing Kiosk (Week 9: Laravel + CRUD + DB)
|--------------------------------------------------------------------------
| Pembagian Modul Kelompok:
| 1. Modul Anggota A (Zahra Zusseta Stiawan) -> Beranda (/), Wizard Cetak (/cetak), Create Order
| 2. Modul Anggota B (Damar Ghulamul Abror)  -> Login (/login), Admin (/admin), Read/Update/Delete Order & Kiosk
*/

// ============================================================================
// 1. ROUTE MODUL ANGGOTA A (ZAHRA) - SISI PENGGUNA (INDEX & CETAK)
// ============================================================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/index.php', [HomeController::class, 'index']);

Route::get('/cetak', [CetakController::class, 'index'])->name('cetak');
Route::get('/cetak.php', [CetakController::class, 'index']);
Route::post('/cetak', [CetakController::class, 'store'])->name('orders.store');
Route::post('/cetak.php', [CetakController::class, 'store']);
Route::post('/orders', [CetakController::class, 'store']);

// ============================================================================
// 2. ROUTE MODUL ANGGOTA B (DAMAR) - SISI OPERATOR & ADMIN (LOGIN, ADMIN & CRUD)
// ============================================================================
Route::get('/login', [AdminController::class, 'login'])->name('login');
Route::get('/login.php', [AdminController::class, 'login']);

Route::get('/admin', [AdminController::class, 'index'])->name('admin');
Route::get('/admin.php', [AdminController::class, 'index']);
Route::post('/admin', [AdminController::class, 'handleAction'])->name('admin.action');
Route::post('/admin.php', [AdminController::class, 'handleAction']);

// Operasi CRUD Pesanan & Kiosk RESTful dari Dashboard Admin
Route::post('/admin/orders', [CetakController::class, 'store'])->name('admin.orders.store');
Route::post('/admin/orders/purge', [AdminController::class, 'purgeCompleted'])->name('admin.orders.purge');
Route::post('/admin/orders/{id}/update', [AdminController::class, 'updateOrder'])->name('admin.orders.update');
Route::post('/admin/orders/{id}/delete', [AdminController::class, 'deleteOrder'])->name('admin.orders.delete');
Route::post('/admin/kiosks/{id}/update', [AdminController::class, 'updateKiosk'])->name('admin.kiosks.update');

// Endpoint JSON Sinkronisasi Real-Time CepatikDB <-> Database SQL Laravel
Route::get('/api/orders', [AdminController::class, 'apiOrders']);
Route::post('/api/orders', [CetakController::class, 'store']);
Route::post('/api/orders/purge', [AdminController::class, 'purgeCompleted']);
Route::post('/api/orders/{ticket}/status', [AdminController::class, 'updateOrder']);
Route::post('/api/orders/{ticket}/delete', [AdminController::class, 'deleteOrder']);
