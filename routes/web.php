<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockRequestController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::get('/api/products', [ProductController::class, 'api']);

    // Transactions
    Route::resource('transactions', TransactionController::class);
});


Route::middleware('auth')->group(function () {
    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::post('/stock-requests/{stockRequest}/approve', [StockRequestController::class, 'approve'])->name('admin.stockRequests.approve');
        Route::post('/stock-requests/{stockRequest}/reject', [StockRequestController::class, 'reject'])->name('admin.stockRequests.reject');
    });

    // User routes
    Route::middleware('role:user,admin')->group(function () {
        Route::get('/stock-requests', [StockRequestController::class, 'index'])->name('admin.stockRequests.index');
        Route::get('/stock-requests/create', [StockRequestController::class, 'create'])->name('user.stockRequests.create'); // 👈 ini route GET
        Route::post('/stock-requests', [StockRequestController::class, 'store'])->name('user.stockRequests.store');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
