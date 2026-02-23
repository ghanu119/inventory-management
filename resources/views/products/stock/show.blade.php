@extends('layouts.app')

@section('title', $product->name . ' - Stock Details')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
            <p class="text-gray-600 mt-1">SKU: {{ $product->sku }}</p>
        </div>
        <a href="{{ route('stock.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Back</a>
    </div>

    @if (session('success'))
    <div class="p-4 bg-green-100 text-green-800 rounded-md">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-3 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900">Current Stock</h3>
            <p class="text-4xl font-bold mt-2 {{ $product->stock_quantity <= 10 ? 'text-red-600' : 'text-green-600' }}">
                {{ $product->stock_quantity }}
            </p>
            <p class="text-sm text-gray-600 mt-1">units</p>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900">Price</h3>
            <p class="text-2xl font-bold mt-2">Rs. {{ number_format($product->price, 2) }}</p>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900">GST Rate</h3>
            <p class="text-2xl font-bold mt-2">{{ number_format($product->gst_rate, 2) }}%</p>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="flex gap-4">
                <button type="button" id="openStockInModalBtn" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Stock In</button>
                <a href="{{ route('stock.out', $product) }}" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Stock Out</a>
                <a href="{{ route('stock.adjust', $product) }}" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">Adjust Stock</a>
                <a href="{{ route('stock.history', $product) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">View History</a>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Stock Transactions</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Before/After</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentHistories ?? [] as $history)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $history->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-3 py-1 rounded-full text-white text-xs font-medium
                                    {{ $history->type === 'in' ? 'bg-green-600' : ($history->type === 'out' ? 'bg-red-600' : 'bg-orange-600') }}">
                                    {{ ucfirst($history->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $history->type === 'out' ? '-' : '+' }}{{ $history->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ucfirst(str_replace('_', ' ', $history->reason)) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $history->stock_before }} → {{ $history->stock_after }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No transactions yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Stock In modal (serial numbers in modal) --}}
<div id="stockInModal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog" aria-label="Stock In">
    <div class="fixed inset-0 bg-black/60" id="stockInModalBackdrop"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col my-auto">
            <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center shrink-0">
                <h2 class="text-lg font-semibold text-gray-900">Stock In: {{ $product->name }}</h2>
                <button type="button" id="closeStockInModal" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100" aria-label="Close">&times;</button>
            </div>
            <div class="p-4 overflow-y-auto flex-1">
                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md text-red-700 text-sm">
                        @foreach($errors->all() as $err) <p>{{ $err }}</p> @endforeach
                    </div>
                @endif
                <form action="{{ route('stock.in.store', $product) }}" method="POST" class="space-y-4" id="stockInModalForm">
                    @csrf
                    <input type="hidden" name="from_modal" value="1">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="modal_quantity" class="block text-sm font-semibold text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity" id="modal_quantity" min="1" value="{{ old('quantity', 1) }}" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="modal_reason" class="block text-sm font-semibold text-gray-700 mb-1">Reason <span class="text-red-500">*</span></label>
                            <select name="reason" id="modal_reason" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-indigo-500">
                                <option value="purchase" {{ old('reason') === 'purchase' ? 'selected' : '' }}>Purchase</option>
                                <option value="return" {{ old('reason') === 'return' ? 'selected' : '' }}>Return from Customer</option>
                                <option value="damage_correction" {{ old('reason') === 'damage_correction' ? 'selected' : '' }}>Damage Correction</option>
                                <option value="adjustment" {{ old('reason') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                            </select>
                        </div>
                    </div>
                    <div id="modalSerialSection" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Serial numbers <span class="text-red-500">*</span> (one per unit)</label>
                        <p class="text-xs text-gray-500 mb-2">Enter or scan a serial for each unit. Use the scan button next to each field.</p>
                        <div class="flex flex-wrap gap-2 mb-2">
                            <button type="button" id="modalScanBarcodeBtn" data-no-loader class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">Scan with webcam</button>
                        </div>
                        <div id="modalSerialInputs" class="space-y-2"></div>
                    </div>
                    <div>
                        <label for="modal_reference_id" class="block text-sm font-semibold text-gray-700 mb-1">Reference ID</label>
                        <input type="text" name="reference_id" id="modal_reference_id" value="{{ old('reference_id') }}" placeholder="e.g. PO-2024-001" class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="modal_notes" class="block text-sm font-semibold text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" id="modal_notes" rows="2" class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                    </div>
                    <p class="text-sm text-gray-600">Current stock: <strong>{{ $product->stock_quantity }}</strong> → After: <strong id="modalNewStock">{{ $product->stock_quantity }}</strong> units</p>
                    <div class="flex flex-wrap gap-2 pt-2 items-center">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Confirm Stock In</button>
                        <button type="button" id="cancelStockInModal" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Cancel</button>
                        <a href="{{ route('stock.in', $product) }}" class="text-sm text-gray-500 hover:text-indigo-600">Use full page form</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('components.barcode-scanner')

<script>
(function() {
    const modal = document.getElementById('stockInModal');
    const openBtn = document.getElementById('openStockInModalBtn');
    const closeBtn = document.getElementById('closeStockInModal');
    const cancelBtn = document.getElementById('cancelStockInModal');
    const backdrop = document.getElementById('stockInModalBackdrop');
    const quantityEl = document.getElementById('modal_quantity');
    const serialSection = document.getElementById('modalSerialSection');
    const serialInputs = document.getElementById('modalSerialInputs');
    const oldSerials = @json(old('serial_numbers', []));
    const currentStock = {{ $product->stock_quantity }};

    function openModal() {
        if (modal) modal.classList.remove('hidden');
        updateModalSerials();
    }
    function closeModal() {
        if (modal) modal.classList.add('hidden');
    }

    function updateModalSerials() {
        const qty = parseInt(quantityEl.value) || 0;
        document.getElementById('modalNewStock').textContent = currentStock + qty;
        serialInputs.innerHTML = '';
        if (qty > 0) {
            serialSection.classList.remove('hidden');
            for (let i = 1; i <= qty; i++) {
                const val = (Array.isArray(oldSerials) && oldSerials[i - 1] != null) ? String(oldSerials[i - 1]).replace(/"/g, '&quot;') : '';
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2';
                div.innerHTML = '<label class="text-sm text-gray-600 w-20">Serial ' + i + '</label><input type="text" name="serial_numbers[]" class="flex-1 px-4 py-2 border rounded-md" placeholder="Enter or scan" value="' + val + '" required><button type="button" class="modal-serial-scan p-2 border rounded-md text-gray-600 hover:bg-gray-100" data-no-loader title="Scan">📷</button>';
                const input = div.querySelector('input');
                const scanBtn = div.querySelector('.modal-serial-scan');
                if (scanBtn && window.openBarcodeScanner) {
                    scanBtn.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        window.openBarcodeScanner(function(v) { input.value = v; return true; });
                    });
                }
                serialInputs.appendChild(div);
            }
        } else {
            serialSection.classList.add('hidden');
        }
    }

    if (openBtn) openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    if (backdrop) backdrop.addEventListener('click', closeModal);
    if (quantityEl) {
        quantityEl.addEventListener('input', updateModalSerials);
        quantityEl.addEventListener('change', updateModalSerials);
    }
    if (document.getElementById('modalScanBarcodeBtn') && window.openBarcodeScanner) {
        document.getElementById('modalScanBarcodeBtn').addEventListener('mousedown', function(e) {
            e.preventDefault();
            window.openBarcodeScanner();
        });
    }

    @if(session('open_stock_in_modal'))
    openModal();
    @endif
})();
</script>
@endsection
