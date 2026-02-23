<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService
    ) {}

    public function index(Request $request)
    {
        $query = Invoice::with('customer');

        if ($request->has('search')) {
            $query->where('invoice_number', 'like', "%{$request->search}%")
                ->orWhereHas('customer', function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%");
                });
        }

        $invoices = $query->latest()->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $products = Product::availableStock()
            ->select(['id', 'name', 'sku', 'price', 'gst_rate', 'stock_quantity', 'custom_short_text', 'hsn_code', 'warranty_years', 'is_gst_included'])
            ->orderBy('name')
            ->limit(config('app.max_products_for_dropdown', 5000))
            ->get();

        return view('invoices.create', compact('products'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product']);
        $products = Product::query()
            ->select(['id', 'name', 'sku', 'price', 'gst_rate', 'stock_quantity', 'custom_short_text', 'hsn_code', 'warranty_years', 'is_gst_included'])
            ->orderBy('name')
            ->limit(config('app.max_products_for_dropdown', 5000))
            ->get();

        return view('invoices.edit', compact('invoice', 'products'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        try {
            $invoice = $this->invoiceService->createInvoice($request->validated());

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        try {
            $this->invoiceService->updateInvoice($invoice, $request->validated());

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Invoice $invoice)
    {
        try {
            $this->invoiceService->deleteInvoice($invoice);

            return redirect()->route('invoices.index')
                ->with('success', 'Invoice deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('invoices.index')
                ->with('error', $e->getMessage());
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product']);

        return view('invoices.show', compact('invoice'));
    }

    public function pdf(Request $request, Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product']);
        $company = \App\Models\Company::getCompany();

        $data = compact('invoice', 'company');
        $options = [];
        if (!is_file(public_path('fonts/NotoSansGujarati-Regular.ttf'))) {
            $options['default_font'] = 'dejavusans';
        }

        try {
            $pdf = PDF::loadView('invoices.pdf', $data, [], $options);
            $filename = "invoice-{$invoice->invoice_number}.pdf";
            return $pdf->stream($filename);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Could not generate PDF: ' . $e->getMessage());
        }
    }
}
