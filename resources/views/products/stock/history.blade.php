@extends('layouts.app')

@section('title', 'Stock History - ' . $product->name)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Stock History</h1>
            <p class="text-gray-600 mt-1">{{ $product->name }} ({{ $product->sku }})</p>
        </div>
        <div class="flex items-center gap-2">
            @if($histories->total() > 0)
                @php
                    $confirmMsg = $linkedInvoicesCount > 0
                        ? __('This will clear all stock history for this product. :count linked invoice(s) will also be removed. This cannot be undone. Continue?', ['count' => $linkedInvoicesCount])
                        : __('This will clear all stock history for this product. This cannot be undone. Continue?');
                @endphp
                <form method="post" action="{{ route('stock.history.clear', $product) }}" class="inline" onsubmit="return confirm({{ json_encode($confirmMsg) }});">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Clear stock history</button>
                </form>
            @endif
            <a href="{{ route('stock.show', $product) }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Back</a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="get" action="{{ route('stock.history', $product) }}" class="mb-4 flex flex-wrap items-center gap-2">
                <label for="history-serial-search" class="text-sm font-medium text-gray-700">Search by serial number</label>
                <input type="text" name="serial" id="history-serial-search" value="{{ $serialSearch ?? '' }}" placeholder="e.g. TR32 or 653461989157" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 max-w-xs">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">Search</button>
                @if($serialSearch !== null && $serialSearch !== '')
                    <a href="{{ route('stock.history', $product) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm">Clear</a>
                @endif
            </form>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serial numbers</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Before</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock After</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($histories as $history)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $history->created_at->format('d M Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-3 py-1 rounded-full text-white text-xs font-medium
                                    {{ $history->type === 'in' ? 'bg-green-600' : ($history->type === 'out' ? 'bg-red-600' : 'bg-orange-600') }}">
                                    {{ strtoupper($history->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $history->type === 'out' ? '-' : '+' }}{{ $history->quantity }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($history->type === 'in' && $history->productSerials->isNotEmpty())
                                    <ul class="list-disc list-inside space-y-0.5">
                                        @foreach($history->productSerials as $ps)
                                            <li>{{ $ps->serial_number }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ucfirst(str_replace('_', ' ', $history->reason)) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $history->stock_before }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $history->stock_after }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $history->reference_id ?? 'N/A' }}
                            </td>
                        </tr>
                        @if($history->notes)
                        <tr class="bg-gray-50">
                            <td colspan="8" class="px-6 py-4 text-sm text-gray-600">
                                <strong>Notes:</strong> {{ $history->notes }}
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                @if($serialSearch !== null && trim($serialSearch ?? '') !== '')
                                    No stock-in entries found for this serial number. Try a different search or <a href="{{ route('stock.history', $product) }}" class="text-indigo-600 hover:underline">clear search</a>.
                                @else
                                    No stock history found
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $histories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
