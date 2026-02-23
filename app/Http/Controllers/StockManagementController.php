<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\UniqueConstraintViolationException;

class StockManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('sku', 'like', "%{$request->search}%");
        }

        if ($request->get('status') === 'low') {
            $query->lowStock(10);
        }

        $products = $query->paginate(15);

        return view('products.stock.index', compact('products'));
    }

    public function show(Product $product)
    {
        $recentHistories = $product->stockHistories()->limit(10)->get();

        return view('products.stock.show', compact('product', 'recentHistories'));
    }

    public function stockIn(Product $product)
    {
        return view('products.stock.in', compact('product'));
    }

    public function storeStockIn(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:500'],
            'serial_numbers' => ['required', 'array', 'max:500'],
            'serial_numbers.*' => ['required', 'string', 'max:100'],
            'reason' => ['required', 'string', 'in:purchase,return,damage_correction,adjustment'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'reference_id' => ['nullable', 'string', 'max:100'],
        ], [
            'serial_numbers.*.required' => 'Each serial number is required.',
            'serial_numbers.max' => 'Maximum 500 serial numbers per stock-in.',
        ]);

        $quantity = (int) $validated['quantity'];
        $serials = $validated['serial_numbers'] ?? [];
        if (count($serials) !== $quantity) {
            return $this->stockInErrorRedirect($request, $product, ['serial_numbers' => "You must enter exactly {$quantity} serial numbers (one per unit)."]);
        }

        $serialsToAdd = array_values(array_filter(array_map('trim', $validated['serial_numbers'])));
        $existing = ProductSerial::where('product_id', $product->id)
            ->whereIn('serial_number', $serialsToAdd)
            ->pluck('serial_number')
            ->all();
        if (! empty($existing)) {
            $list = implode(', ', array_slice($existing, 0, 5));
            if (count($existing) > 5) {
                $list .= ' (and ' . (count($existing) - 5) . ' more)';
            }

            return $this->stockInErrorRedirect($request, $product, [
                'serial_numbers' => __('The following serial number(s) are already registered for this product: :list', ['list' => $list]),
            ]);
        }

        try {
            DB::transaction(function () use ($product, $validated, $serialsToAdd) {
                $history = $product->addStock(
                    $validated['quantity'],
                    $validated['reason'],
                    $validated['notes'] ?? null,
                    $validated['reference_id'] ?? null
                );

                foreach ($serialsToAdd as $serialNumber) {
                    ProductSerial::create([
                        'product_id' => $product->id,
                        'stock_history_id' => $history->id,
                        'serial_number' => $serialNumber,
                        'status' => 'available',
                    ]);
                }
            });
        } catch (UniqueConstraintViolationException $e) {
            return $this->stockInErrorRedirect($request, $product, [
                'serial_numbers' => __('A serial number you entered is already registered for this product. Please remove duplicates and try again.'),
            ]);
        }

        return redirect()->route('stock.show', $product)
            ->with('success', "Stock increased by {$validated['quantity']} units");
    }

    private function stockInErrorRedirect(Request $request, Product $product, array $errors)
    {
        $redirect = redirect()->withErrors($errors)->withInput();
        if ($request->input('from_modal')) {
            return $redirect->to(route('stock.show', $product))->with('open_stock_in_modal', true);
        }

        return $redirect->back();
    }

    public function stockOut(Product $product)
    {
        return view('products.stock.out', compact('product'));
    }

    public function storeStockOut(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:' . $product->stock_quantity],
            'serial_numbers' => ['required', 'array', 'max:500'],
            'serial_numbers.*' => ['required', 'string', 'max:100'],
            'reason' => ['required', 'string', 'in:sale,damage,loss,return_to_supplier'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'reference_id' => ['nullable', 'string', 'max:100'],
        ], [
            'serial_numbers.*.required' => 'Each serial number is required.',
        ]);

        $quantity = (int) $validated['quantity'];
        $serials = array_map('trim', $validated['serial_numbers']);
        $serials = array_values(array_filter($serials));

        if (count($serials) !== $quantity) {
            return back()->withErrors(['serial_numbers' => "You must enter exactly {$quantity} serial numbers (one per unit)."])->withInput();
        }

        $availableSerials = ProductSerial::availableForProduct($product->id)->pluck('serial_number')->all();
        $missing = array_diff($serials, $availableSerials);
        if (! empty($missing)) {
            return back()->withErrors(['serial_numbers' => 'The following serial numbers are not available for this product: ' . implode(', ', array_slice($missing, 0, 5)) . (count($missing) > 5 ? ' (and ' . (count($missing) - 5) . ' more)' : '')])->withInput();
        }

        try {
            DB::transaction(function () use ($product, $validated, $serials) {
                foreach ($serials as $serialNumber) {
                    ProductSerial::where('product_id', $product->id)
                        ->where('serial_number', $serialNumber)
                        ->where('status', 'available')
                        ->update(['status' => 'sold']);
                }

                $product->reduceStock(
                    $validated['quantity'],
                    $validated['reason'],
                    $validated['reference_id'] ?? null
                );
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['serial_numbers' => 'Stock out failed. Please try again.'])->withInput();
        }

        return redirect()->route('stock.show', $product)
            ->with('success', "Stock decreased by {$quantity} units");
    }

    public function adjust(Product $product)
    {
        return view('products.stock.adjust', compact('product'));
    }

    public function storeAdjust(Request $request, Product $product)
    {
        $validated = $request->validate([
            'new_quantity' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $product->adjustStock(
            $validated['new_quantity'],
            $validated['reason'],
            $validated['notes'] ?? null
        );

        return redirect()->route('stock.show', $product)
            ->with('success', 'Stock adjusted successfully');
    }

    public function history(Request $request, Product $product)
    {
        $query = $product->stockHistories()->with('productSerials');
        $serialSearch = $request->input('serial');
        if ($serialSearch !== null && trim($serialSearch) !== '') {
            $term = trim($serialSearch);
            $query->whereHas('productSerials', function ($q) use ($term) {
                $q->where('serial_number', 'like', '%' . $term . '%');
            });
        }
        $histories = $query->paginate(20)->withQueryString();
        $serialSearch = $request->input('serial');

        return view('products.stock.history', compact('product', 'histories', 'serialSearch'));
    }
}
