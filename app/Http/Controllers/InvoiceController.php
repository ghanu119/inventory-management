<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\StockHistory;
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

    /**
     * Show the form to truncate invoices (e.g. at end of year). Stock is reverted for each deleted invoice.
     */
    public function truncateForm(Request $request)
    {
        $beforeDate = $request->input('before_date');
        $query = Invoice::query();
        if ($beforeDate) {
            $query->whereDate('invoice_date', '<=', $beforeDate);
        }
        $invoicesCount = $query->count();

        return view('invoices.truncate', [
            'invoicesCount' => $invoicesCount,
            'beforeDate' => $beforeDate,
        ]);
    }

    /**
     * Truncate invoices and revert stock. Optional: only invoices on or before the given date.
     */
    public function truncate(Request $request)
    {
        $validated = $request->validate([
            'before_date' => ['nullable', 'date'],
            'confirm' => ['required', 'in:1'],
            'truncate_stock_history' => ['nullable', 'boolean'],
        ], [
            'confirm.in' => __('You must confirm that you want to truncate invoices and revert stock.'),
        ]);

        $query = Invoice::query()->orderBy('invoice_date');
        if (! empty($validated['before_date'])) {
            $query->whereDate('invoice_date', '<=', $validated['before_date']);
        }
        $invoices = $query->get();

        foreach ($invoices as $invoice) {
            $this->invoiceService->deleteInvoice($invoice);
        }

        $invoiceCount = $invoices->count();
        $truncateHistory = ! empty($validated['truncate_stock_history']);
        $historyDeleted = 0;

        if ($truncateHistory) {
            $historyDeleted = StockHistory::query()->count();
            $serialsDeleted = ProductSerial::query()->count();
            StockHistory::query()->delete();
            ProductSerial::query()->delete();
            Product::query()->update(['stock_quantity' => 0]);
        }

        $messages = [];
        if ($invoiceCount > 0) {
            $messages[] = __(':count invoice(s) truncated. Stock has been reverted for all affected products.', ['count' => $invoiceCount]);
        }
        if ($truncateHistory) {
            if ($historyDeleted > 0) {
                $messages[] = __('Stock history cleared for all products (:count record(s) removed).', ['count' => $historyDeleted]);
            }
            if ($serialsDeleted > 0) {
                $messages[] = __('All serial numbers cleared (:count removed). New stock-in entries will create new serials.', ['count' => $serialsDeleted]);
            }
            $messages[] = __('Current stock set to zero for all products.');
        }
        if (empty($messages)) {
            $messages[] = __('No invoices to truncate.');
        }

        return redirect()->route('invoices.truncate')
            ->with('success', implode(' ', $messages));
    }

    /**
     * Clear stock history and serial numbers only (no invoice deletion). Use when invoices are already truncated but old serials still appear in dropdowns.
     */
    public function clearHistoryAndSerials(Request $request)
    {
        $request->validate([
            'confirm' => ['required', 'in:1'],
        ], [
            'confirm.in' => __('You must confirm to clear stock history and serial numbers.'),
        ]);

        $historyDeleted = StockHistory::query()->count();
        $serialsDeleted = ProductSerial::query()->count();
        StockHistory::query()->delete();
        ProductSerial::query()->delete();
        Product::query()->update(['stock_quantity' => 0]);

        $messages = [];
        if ($historyDeleted > 0) {
            $messages[] = __('Stock history cleared (:count record(s) removed).', ['count' => $historyDeleted]);
        }
        if ($serialsDeleted > 0) {
            $messages[] = __('All serial numbers cleared (:count removed). Invoice serial dropdowns will now show only serials from new stock-in entries.', ['count' => $serialsDeleted]);
        }
        $messages[] = __('Current stock set to zero for all products.');

        return redirect()->route('invoices.truncate')
            ->with('success', implode(' ', $messages));
    }
}
