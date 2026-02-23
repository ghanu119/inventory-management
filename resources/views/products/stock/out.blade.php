@extends('layouts.app')

@section('title', 'Stock Out - ' . $product->name)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Stock Out: {{ $product->name }}</h1>
        <a href="{{ route('stock.show', $product) }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Back</a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('stock.out.store', $product) }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" id="quantity" min="1" max="{{ $product->stock_quantity }}" value="{{ old('quantity') }}" required class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('quantity') border-red-500 @enderror">
                    @error('quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Max: {{ $product->stock_quantity }} units</p>
                </div>

                <div>
                    <label for="reason" class="block text-sm font-semibold text-gray-700 mb-2">Reason <span class="text-red-500">*</span></label>
                    <select name="reason" id="reason" required class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('reason') border-red-500 @enderror">
                        <option value="">Select Reason</option>
                        <option value="sale" {{ old('reason') === 'sale' ? 'selected' : '' }}>Sale</option>
                        <option value="damage" {{ old('reason') === 'damage' ? 'selected' : '' }}>Damage</option>
                        <option value="loss" {{ old('reason') === 'loss' ? 'selected' : '' }}>Loss/Theft</option>
                        <option value="return_to_supplier" {{ old('reason') === 'return_to_supplier' ? 'selected' : '' }}>Return to Supplier</option>
                    </select>
                    @error('reason')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div id="serialNumbersSection" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Serial numbers <span class="text-red-500">*</span> (one per unit)</label>
                <p class="text-xs text-gray-500 mb-2">Enter or scan the serial number for each unit being removed.</p>
                <div class="flex flex-wrap gap-2 mb-2">
                    <button type="button" id="scanBarcodeBtn" data-no-loader class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13v4a2 2 0 01-2 2H7a2 2 0 01-2-2v-4"/></svg>
                        Scan with webcam
                    </button>
                </div>
                <div id="serialInputs" class="space-y-2"></div>
                @error('serial_numbers')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="reference_id" class="block text-sm font-semibold text-gray-700 mb-2">Reference ID (e.g., Invoice Number)</label>
                <input type="text" name="reference_id" id="reference_id" value="{{ old('reference_id') }}" placeholder="e.g., INV-2024-001" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="4" placeholder="Additional notes..." class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
            </div>

            <div class="p-4 bg-red-50 rounded-md border border-red-200">
                <p class="text-sm text-red-800">
                    <strong>Current Stock:</strong> {{ $product->stock_quantity }} units<br>
                    <strong>After this transaction:</strong> <span id="newStock">{{ $product->stock_quantity }}</span> units
                </p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Confirm Stock Out</button>
                <a href="{{ route('stock.show', $product) }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</a>
            </div>
        </form>
    </div>
</div>

@include('components.barcode-scanner')

<script>
    const quantityEl = document.getElementById('quantity');
    const serialSection = document.getElementById('serialNumbersSection');
    const serialInputs = document.getElementById('serialInputs');
    const maxStock = {{ $product->stock_quantity }};
    const oldSerialNumbers = @json(old('serial_numbers', []));

    function updateSerialInputs() {
        const quantity = Math.min(parseInt(quantityEl.value) || 0, maxStock);
        const currentStock = {{ $product->stock_quantity }};
        document.getElementById('newStock').textContent = currentStock - quantity;

        serialInputs.innerHTML = '';
        if (quantity > 0) {
            serialSection.classList.remove('hidden');
            for (let i = 1; i <= quantity; i++) {
                const prevValue = (Array.isArray(oldSerialNumbers) && oldSerialNumbers[i - 1] != null) ? String(oldSerialNumbers[i - 1]) : '';
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2';
                div.innerHTML = '<label class="text-sm text-gray-600 w-24">Serial ' + i + '</label>' +
                    '<input type="text" name="serial_numbers[]" class="flex-1 px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Enter or scan serial number" value="' + prevValue.replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '" required>' +
                    '<button type="button" class="serial-scan-btn inline-flex items-center p-2 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-100" data-no-loader title="Scan barcode for this field"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg></button>';
                const input = div.querySelector('input');
                const scanBtn = div.querySelector('.serial-scan-btn');
                scanBtn.addEventListener('mousedown', function(ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    if (window.openBarcodeScanner) {
                        window.openBarcodeScanner(function(value) {
                            input.value = value;
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                            return true;
                        });
                    }
                });
                serialInputs.appendChild(div);
            }
        } else {
            serialSection.classList.add('hidden');
        }
    }

    document.getElementById('scanBarcodeBtn').addEventListener('mousedown', function(ev) {
        ev.preventDefault();
        ev.stopPropagation();
        if (window.openBarcodeScanner) window.openBarcodeScanner();
    });

    quantityEl.addEventListener('change', updateSerialInputs);
    quantityEl.addEventListener('input', updateSerialInputs);
    updateSerialInputs();
</script>
@endsection
