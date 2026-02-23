<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductSerial;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            // Find or create customer
            $customer = Customer::findOrCreateByContact(
                $data['customer_phone'] ?? '',
                $data['customer_email'] ?? '',
                [
                    'name' => $data['customer_name'],
                    'address' => $data['customer_address'] ?? null,
                    'gst_number' => $data['customer_gst_number'] ?? null,
                ]
            );

            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'invoice_date' => $data['invoice_date'] ?? now(),
                'customer_id' => $customer->id,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'discount_type' => $data['discount_type'] ?? null,
                'payment_mode' => $data['payment_mode'] ?? 'Cash',
                'notes' => $data['notes'] ?? null,
            ]);

            $subtotal = 0;
            $totalGst = 0;

            // Process invoice items
            foreach ($data['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $quantity = $itemData['quantity'];

                // Check stock availability
                if (!$product->hasStock($quantity)) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Calculate amounts based on whether price includes GST
                $price = $product->price;
                $gstRate = $product->gst_rate;

                if ($product->is_gst_included) {
                    // Price already includes GST:
                    // total_amount = quantity * price (no extra GST added on top)
                    $totalAmount = $price * $quantity;

                    if ($gstRate > 0) {
                        // Extract taxable amount and GST component from GST-inclusive price
                        $taxableAmount = $totalAmount / (1 + ($gstRate / 100));
                        $gstAmount = $totalAmount - $taxableAmount;
                    } else {
                        $taxableAmount = $totalAmount;
                        $gstAmount = 0;
                    }
                } else {
                    // Price is without GST:
                    // taxable_amount = quantity * price, GST added on top
                    $taxableAmount = $price * $quantity;
                    $gstAmount = ($taxableAmount * $gstRate) / 100;
                    $totalAmount = $taxableAmount + $gstAmount;
                }

                $cgstAmount = $gstAmount / 2;
                $sgstAmount = $gstAmount / 2;

                $warrantyYears = $itemData['warranty_years'] ?? $product->warranty_years;
                $customShortText = $itemData['custom_short_text'] ?? $product->custom_short_text;

                // Create invoice item
                $invoiceItem = InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'serial_no' => $itemData['serial_no'] ?? null,
                    'warranty_years' => $warrantyYears,
                    'custom_short_text' => $customShortText,
                    'quantity' => $quantity,
                    'price' => $price,
                    'gst_rate' => $gstRate,
                    'cgst_amount' => $cgstAmount,
                    'sgst_amount' => $sgstAmount,
                    'taxable_amount' => $taxableAmount,
                    'total_amount' => $totalAmount,
                ]);

                // If serial_no matches an available ProductSerial, mark it as sold
                $serialNo = trim($itemData['serial_no'] ?? '');
                if ($serialNo !== '') {
                    ProductSerial::where('product_id', $product->id)
                        ->where('serial_number', $serialNo)
                        ->where('status', 'available')
                        ->update(['status' => 'sold', 'invoice_item_id' => $invoiceItem->id]);
                }

                // Reduce stock and record invoice reference for traceability
                $product->reduceStock($quantity, 'sale', $invoice->invoice_number);

                $subtotal += $taxableAmount;
                $totalGst += $gstAmount;
            }

            // Calculate discount
            $discountAmount = 0;
            if ($invoice->discount_type === 'percentage' && $invoice->discount_amount > 0) {
                $discountAmount = ($subtotal * $invoice->discount_amount) / 100;
            } elseif ($invoice->discount_type === 'flat') {
                $discountAmount = $invoice->discount_amount;
            }

            // Calculate grand total
            $grandTotal = $subtotal + $totalGst - $discountAmount;

            // Update invoice totals
            $invoice->update([
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_gst' => $totalGst,
                'grand_total' => $grandTotal,
            ]);

            return $invoice->fresh(['customer', 'items.product']);
        });
    }

    public function deleteInvoice(Invoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            $invoice->load('items.product');
            foreach ($invoice->items as $item) {
                $item->product->addStock($item->quantity, 'return', null, $invoice->invoice_number . ' (deleted)');
                ProductSerial::where('invoice_item_id', $item->id)->update(['status' => 'available', 'invoice_item_id' => null]);
            }
            $invoice->delete();
        });
    }

    public function updateInvoice(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            $invoice->load('items.product');
            // Revert stock and serials for all current items
            foreach ($invoice->items as $item) {
                $item->product->addStock($item->quantity, 'return', null, $invoice->invoice_number . ' (edit)');
                ProductSerial::where('invoice_item_id', $item->id)->update(['status' => 'available', 'invoice_item_id' => null]);
            }
            $invoice->items()->delete();

            // Update invoice header
            $customer = Customer::findOrCreateByContact(
                $data['customer_phone'] ?? '',
                $data['customer_email'] ?? '',
                [
                    'name' => $data['customer_name'],
                    'address' => $data['customer_address'] ?? null,
                    'gst_number' => $data['customer_gst_number'] ?? null,
                ]
            );
            $invoice->update([
                'invoice_date' => $data['invoice_date'] ?? $invoice->invoice_date,
                'customer_id' => $customer->id,
                'discount_amount' => 0,
                'discount_type' => $data['discount_type'] ?? null,
                'payment_mode' => $data['payment_mode'] ?? $invoice->payment_mode,
                'notes' => $data['notes'] ?? null,
            ]);

            $subtotal = 0;
            $totalGst = 0;
            foreach ($data['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $quantity = (int) $itemData['quantity'];
                if (!$product->hasStock($quantity)) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }
                $price = $product->price;
                $gstRate = $product->gst_rate;
                if ($product->is_gst_included) {
                    $totalAmount = $price * $quantity;
                    if ($gstRate > 0) {
                        $taxableAmount = $totalAmount / (1 + ($gstRate / 100));
                        $gstAmount = $totalAmount - $taxableAmount;
                    } else {
                        $taxableAmount = $totalAmount;
                        $gstAmount = 0;
                    }
                } else {
                    $taxableAmount = $price * $quantity;
                    $gstAmount = ($taxableAmount * $gstRate) / 100;
                    $totalAmount = $taxableAmount + $gstAmount;
                }
                $cgstAmount = $gstAmount / 2;
                $sgstAmount = $gstAmount / 2;
                $warrantyYears = $itemData['warranty_years'] ?? $product->warranty_years;
                $customShortText = $itemData['custom_short_text'] ?? $product->custom_short_text;

                $invoiceItem = InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'serial_no' => $itemData['serial_no'] ?? null,
                    'warranty_years' => $warrantyYears,
                    'custom_short_text' => $customShortText,
                    'quantity' => $quantity,
                    'price' => $price,
                    'gst_rate' => $gstRate,
                    'cgst_amount' => $cgstAmount,
                    'sgst_amount' => $sgstAmount,
                    'taxable_amount' => $taxableAmount,
                    'total_amount' => $totalAmount,
                ]);
                $serialNo = trim($itemData['serial_no'] ?? '');
                if ($serialNo !== '') {
                    ProductSerial::where('product_id', $product->id)
                        ->where('serial_number', $serialNo)
                        ->where('status', 'available')
                        ->update(['status' => 'sold', 'invoice_item_id' => $invoiceItem->id]);
                }
                $product->reduceStock($quantity, 'sale', $invoice->invoice_number);
                $subtotal += $taxableAmount;
                $totalGst += $gstAmount;
            }

            $discountAmount = 0;
            if ($invoice->discount_type === 'percentage' && ($data['discount_amount'] ?? 0) > 0) {
                $discountAmount = ($subtotal * (float) $data['discount_amount']) / 100;
            } elseif (($data['discount_type'] ?? '') === 'flat' && ($data['discount_amount'] ?? 0) > 0) {
                $discountAmount = (float) $data['discount_amount'];
            }
            $grandTotal = $subtotal + $totalGst - $discountAmount;
            $invoice->update([
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_gst' => $totalGst,
                'grand_total' => $grandTotal,
            ]);
            return $invoice->fresh(['customer', 'items.product']);
        });
    }
}
