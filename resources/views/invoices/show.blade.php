@extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Invoice: {{ $invoice->invoice_number }}</h1>
        <span class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('invoices.edit', $invoice) }}" class="px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">Edit</a>
            <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm whitespace-nowrap">Print invoice</a>
        </span>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-medium mb-2">Invoice Details</h3>
                <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Date:</strong> {{ $invoice->invoice_date->format('d M Y') }}</p>
                <p><strong>Payment Mode:</strong> {{ $invoice->payment_mode }}</p>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Customer Details</h3>
                <p><strong>Name:</strong> {{ $invoice->customer->name }}</p>
                @if($invoice->customer->phone)
                    <p><strong>Phone:</strong> {{ $invoice->customer->phone }}</p>
                @endif
                @if($invoice->customer->email)
                    <p><strong>Email:</strong> {{ $invoice->customer->email }}</p>
                @endif
                @if($invoice->customer->address)
                    <p><strong>Address:</strong> {{ $invoice->customer->address }}</p>
                @endif
                @if($invoice->customer->gst_number)
                    <p><strong>GST Number:</strong> {{ $invoice->customer->gst_number }}</p>
                @endif
            </div>
        </div>

        <div class="border-t pt-4">
            <h3 class="text-lg font-medium mb-4">Items</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serial No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">GST Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CGST</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SGST</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoice->items as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium">{{ $item->product->name }}</div>
                                @if($item->product->hsn_code)
                                    <div class="text-xs text-gray-500">HSN: {{ $item->product->hsn_code }}</div>
                                @endif
                                @if($item->warranty_years !== null && $item->warranty_years !== '')
                                    <div class="text-xs text-gray-600">Warranty: {{ $item->warranty_years }} years</div>
                                @endif
                                @if($item->custom_short_text)
                                    <div class="text-xs text-gray-600 mt-1">{!! nl2br(e($item->custom_short_text)) !!}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->serial_no ?: '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₹{{ number_format($item->price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->gst_rate }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₹{{ number_format($item->cgst_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₹{{ number_format($item->sgst_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₹{{ number_format($item->total_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="border-t pt-4 mt-6">
            <div class="flex justify-end">
                <div class="w-64">
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">₹{{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Total GST:</span>
                        <span class="font-medium">₹{{ number_format($invoice->total_gst, 2) }}</span>
                    </div>
                    @if($invoice->discount_amount > 0)
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Discount:</span>
                        <span class="font-medium">-₹{{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between py-2 border-t pt-2">
                        <span class="text-lg font-bold">Grand Total:</span>
                        <span class="text-lg font-bold">₹{{ number_format($invoice->grand_total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($invoice->notes)
        <div class="border-t pt-4 mt-6">
            <h3 class="text-lg font-medium mb-2">Notes</h3>
            <p class="text-gray-600">{{ $invoice->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
