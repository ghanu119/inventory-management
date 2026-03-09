<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\FinanceService;

class DashboardController extends Controller
{
    public function __construct(
        private FinanceService $financeService
    ) {}

    public function index()
    {
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $totalInvoices = Invoice::count();
        $totalSales = Invoice::sum('grand_total');
        $recentInvoices = Invoice::with('customer')
            ->latest()
            ->take(5)
            ->get();

        // High demand (sold frequently) but low stock
        $highDemandLowStock = Product::whereHas('invoiceItems')
            ->where('stock_quantity', '<=', 10)
            ->withCount(['invoiceItems'])
            ->orderByDesc('invoice_items_count')
            ->take(5)
            ->get();

        // Low stock alert
        $lowStockProducts = Product::where('stock_quantity', '<=', 10)
            ->orderBy('stock_quantity')
            ->take(10)
            ->get();

        $financeBalances = $this->financeService->getCurrentBalances();

        return view('dashboard', compact(
            'totalProducts',
            'totalCustomers',
            'totalInvoices',
            'totalSales',
            'financeBalances',
            'recentInvoices',
            'highDemandLowStock',
            'lowStockProducts'
        ));
    }
}
