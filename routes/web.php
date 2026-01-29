<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TrashController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
Route::post('forgot-password', [PasswordResetController::class, 'store'])->name('password.email');
Route::get('reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
Route::post('reset-password', [PasswordResetController::class, 'update'])->name('password.update');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', function() { return redirect()->route('dashboard'); });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/sales-by-date', [DashboardController::class, 'getSalesByDate'])->name('dashboard.sales-by-date');

    // Categories
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

    // Products
    Route::resource('products', ProductController::class);
    
    // Customers
    Route::resource('customers', CustomerController::class);

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'sendEmail'])->name('invoices.send');
    Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.status');
    
    // Sales
    Route::get('/sales/history', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/pos', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');

    // Trash / Recycling Bin
    Route::get('/settings/trash', [TrashController::class, 'index'])->name('trash.index');
    Route::post('/settings/trash/{type}/{id}/restore', [TrashController::class, 'restore'])->name('trash.restore');
    Route::delete('/settings/trash/{type}/{id}', [TrashController::class, 'destroy'])->name('trash.destroy');
});

// Robust file serving route
Route::get('/files/{path}', function ($path) {
    if (str_contains($path, '..')) abort(404); // Prevent directory traversal
    
    // Try via Storage facade first (default 'public' disk)
    $diskPath = Storage::disk('public')->path($path);
    
    if (file_exists($diskPath)) {
        $fullPath = $diskPath;
    } else {
        // Fallback to manual path construction if disk config is weird
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) {
            abort(404);
        }
    }

    $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';
    
    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'no-cache, private', // Use no-cache to debug immediate uploads
        'Access-Control-Allow-Origin' => '*',
    ]);
})->where('path', '.*')->name('files.display');