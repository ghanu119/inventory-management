<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockManagementController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// App entry point: always open at / so setup wizard or login shows correctly on launch
Route::get('/', function () {
    if (User::count() === 0) {
        return redirect()->route('install');
    }
    if (! Auth::check()) {
        return redirect()->route('login');
    }
    return redirect()->route('dashboard');
})->name('home');

// Installation (first-run) – no middleware; redirect to login when already installed
Route::get('/install', [InstallController::class, 'index'])->name('install');
Route::post('/install', [InstallController::class, 'store'])->name('install.store');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/force-logout', [LoginController::class, 'forceLogout'])->name('force-logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Company Settings
    Route::get('/company', [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('/company', [CompanyController::class, 'update'])->name('company.update');

    // Account Settings
    Route::get('/account', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/account', [AccountController::class, 'update'])->name('account.update');

    // Customers (truncate routes before resource)
    Route::get('/customers/truncate', [CustomerController::class, 'truncateForm'])->name('customers.truncate');
    Route::post('/customers/truncate', [CustomerController::class, 'truncate'])->name('customers.truncate.store');
    Route::resource('customers', CustomerController::class);

    // Products (available-serials before resource so it matches first)
    Route::get('/products/{product}/available-serials', [ProductController::class, 'availableSerials'])->name('products.available-serials');
    Route::resource('products', ProductController::class);

    // Stock Management
    Route::get('/stock', [StockManagementController::class, 'index'])->name('stock.index');
    Route::get('/stock/{product}', [StockManagementController::class, 'show'])->name('stock.show');
    Route::get('/stock/{product}/in', [StockManagementController::class, 'stockIn'])->name('stock.in');
    Route::post('/stock/{product}/in', [StockManagementController::class, 'storeStockIn'])->name('stock.in.store');
    Route::get('/stock/{product}/out', [StockManagementController::class, 'stockOut'])->name('stock.out');
    Route::post('/stock/{product}/out', [StockManagementController::class, 'storeStockOut'])->name('stock.out.store');
    Route::get('/stock/{product}/adjust', [StockManagementController::class, 'adjust'])->name('stock.adjust');
    Route::post('/stock/{product}/adjust', [StockManagementController::class, 'storeAdjust'])->name('stock.adjust.store');
    Route::get('/stock/{product}/history', [StockManagementController::class, 'history'])->name('stock.history');
    Route::post('/stock/{product}/history/clear', [StockManagementController::class, 'clearHistory'])->name('stock.history.clear');

    // Invoices (truncate routes before resource so /invoices/truncate is not treated as show)
    Route::get('/invoices/truncate', [InvoiceController::class, 'truncateForm'])->name('invoices.truncate');
    Route::post('/invoices/truncate', [InvoiceController::class, 'truncate'])->name('invoices.truncate.store');
    Route::post('/invoices/clear-history-and-serials', [InvoiceController::class, 'clearHistoryAndSerials'])->name('invoices.clear-history-and-serials');
    Route::resource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

    // Help
    Route::get('/help/scanner-setup', function () {
        return view('help.scanner-setup');
    })->name('help.scanner-setup');

    Route::get('/help/clear-cache', function () {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        return redirect()->route('dashboard')->with('success', __('Cache removed'));
    })->name('help.clear-cache');
});
