@extends('layouts.app')

@section('title', 'Adjust Stock - ' . $product->name)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Adjust Stock: {{ $product->name }}</h1>
        <a href="{{ route('stock.show', $product) }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Back</a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('stock.adjust.store', $product) }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="new_quantity" class="block text-sm font-semibold text-gray-700 mb-2">New Stock Quantity <span class="text-red-500">*</span></label>
                <input type="number" name="new_quantity" id="new_quantity" min="0" value="{{ old('new_quantity', $product->stock_quantity) }}" required class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('new_quantity') border-red-500 @enderror">
                @error('new_quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="reason" class="block text-sm font-semibold text-gray-700 mb-2">Adjustment Reason <span class="text-red-500">*</span></label>
                <input type="text" name="reason" id="reason" value="{{ old('reason') }}" placeholder="e.g., Physical count discrepancy, correction" required class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('reason') border-red-500 @enderror">
                @error('reason')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="4" placeholder="Detailed explanation of the adjustment..." class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
            </div>

            <div class="p-4 bg-orange-50 rounded-md border border-orange-200">
                <p class="text-sm text-orange-800">
                    <strong>Current Stock:</strong> {{ $product->stock_quantity }} units<br>
                    <strong>Change Amount:</strong> <span id="changeAmount">0</span> units<br>
                    <strong>New Stock:</strong> <span id="newStock">{{ $product->stock_quantity }}</span> units
                </p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">Save Adjustment</button>
                <a href="{{ route('stock.show', $product) }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    const currentStock = {{ $product->stock_quantity }};
    document.getElementById('new_quantity').addEventListener('change', function() {
        const newQuantity = parseInt(this.value) || 0;
        const change = newQuantity - currentStock;
        document.getElementById('changeAmount').textContent = change > 0 ? '+' + change : change;
        document.getElementById('newStock').textContent = newQuantity;
    });
</script>
@endsection
