@extends('layouts.app')

@section('title', 'Finance')

@section('content')
@php
    $filtersApplied = !empty($filters['type']) || !empty($filters['account']) || !empty($filters['search']) || (isset($filters['from_date']) && $filters['from_date'] !== now()->startOfMonth()->toDateString()) || (isset($filters['to_date']) && $filters['to_date'] !== now()->endOfMonth()->toDateString());
@endphp

<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Finance</h1>
            <p class="text-sm text-gray-600">Simple overview of cash and bank activity.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('finance.transactions.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Add Transaction</a>
            <button type="button" id="openClearAllModal" data-no-loader class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                Clear All
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Cash Balance</p>
            <p class="text-2xl font-semibold text-gray-900 mt-1">&#8377;{{ number_format($balances['cash_balance'], 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Opening: &#8377;{{ number_format($balances['opening_cash'], 2) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Bank Balance</p>
            <p class="text-2xl font-semibold text-gray-900 mt-1">&#8377;{{ number_format($balances['bank_balance'], 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Opening: &#8377;{{ number_format($balances['opening_bank'], 2) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Balance</p>
            <p class="text-2xl font-semibold text-gray-900 mt-1">&#8377;{{ number_format($balances['total_balance'], 2) }}</p>
        </div>
    </div>

    <details class="bg-white border border-gray-200 rounded-lg" {{ $filtersApplied ? 'open' : '' }}>
        <summary class="cursor-pointer px-4 py-3 font-semibold text-gray-800 flex items-center justify-between">
            <span>Filters</span>
            <span class="text-xs text-gray-500">{{ $filtersApplied ? 'Applied' : 'Optional' }}</span>
        </summary>
        <div class="px-4 pb-4 space-y-3">
            <form method="get" action="{{ route('finance.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div>
                    <label for="from_date" class="block text-xs text-gray-600 mb-1">From</label>
                    <input type="date" name="from_date" id="from_date" value="{{ $filters['from_date'] }}">
                </div>
                <div>
                    <label for="to_date" class="block text-xs text-gray-600 mb-1">To</label>
                    <input type="date" name="to_date" id="to_date" value="{{ $filters['to_date'] }}">
                </div>
                <div>
                    <label for="type" class="block text-xs text-gray-600 mb-1">Type</label>
                    <select name="type" id="type" class="select2">
                        <option value="">All</option>
                        <option value="income" @selected($filters['type'] === 'income')>Income</option>
                        <option value="expense" @selected($filters['type'] === 'expense')>Expense</option>
                        <option value="transfer" @selected($filters['type'] === 'transfer')>Transfer</option>
                    </select>
                </div>
                <div>
                    <label for="account" class="block text-xs text-gray-600 mb-1">Account</label>
                    <select name="account" id="account" class="select2">
                        <option value="">All</option>
                        <option value="cash" @selected($filters['account'] === 'cash')>Cash</option>
                        <option value="bank" @selected($filters['account'] === 'bank')>Bank</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="search" class="block text-xs text-gray-600 mb-1">Search Note</label>
                    <input type="text" name="search" id="search" value="{{ $filters['search'] }}" placeholder="Search...">
                </div>
                <div class="md:col-span-6 flex justify-end gap-2">
                    <a href="{{ route('finance.index') }}" class="px-3 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-100">Reset</a>
                    <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Apply</button>
                </div>
            </form>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                <div class="border border-gray-200 rounded-md px-3 py-2">
                    <p class="text-xs text-gray-500">Inflow</p>
                    <p class="text-sm font-semibold text-green-700">&#8377;{{ number_format($periodStats['total_inflow'], 2) }}</p>
                </div>
                <div class="border border-gray-200 rounded-md px-3 py-2">
                    <p class="text-xs text-gray-500">Outflow</p>
                    <p class="text-sm font-semibold text-red-700">&#8377;{{ number_format($periodStats['total_outflow'], 2) }}</p>
                </div>
                <div class="border border-gray-200 rounded-md px-3 py-2">
                    <p class="text-xs text-gray-500">Net</p>
                    <p class="text-sm font-semibold {{ $periodStats['net_change'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        &#8377;{{ number_format($periodStats['net_change'], 2) }}
                    </p>
                </div>
                <div class="border border-gray-200 rounded-md px-3 py-2">
                    <p class="text-xs text-gray-500">Cash Net</p>
                    <p class="text-sm font-semibold {{ $periodStats['cash_net'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        &#8377;{{ number_format($periodStats['cash_net'], 2) }}
                    </p>
                </div>
                <div class="border border-gray-200 rounded-md px-3 py-2">
                    <p class="text-xs text-gray-500">Bank Net</p>
                    <p class="text-sm font-semibold {{ $periodStats['bank_net'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        &#8377;{{ number_format($periodStats['bank_net'], 2) }}
                    </p>
                </div>
            </div>
        </div>
    </details>

    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Transactions</h2>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                @if($filtersApplied)
                    <span class="px-2 py-1 rounded border border-gray-300 text-gray-700">Filtered: {{ $filteredSummary['rows'] }} row(s)</span>
                    <span class="px-2 py-1 rounded border border-green-200 text-green-700">In: &#8377;{{ number_format($filteredSummary['inflow'], 2) }}</span>
                    <span class="px-2 py-1 rounded border border-red-200 text-red-700">Out: &#8377;{{ number_format($filteredSummary['outflow'], 2) }}</span>
                    <span class="px-2 py-1 rounded border {{ $filteredSummary['net'] >= 0 ? 'border-green-200 text-green-700' : 'border-red-200 text-red-700' }}">
                        Net: &#8377;{{ number_format($filteredSummary['net'], 2) }}
                    </span>
                @else
                    <span class="px-2 py-1 rounded border border-gray-300 text-gray-700">All rows: {{ $filteredSummary['rows'] }}</span>
                @endif
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    @php
                        $isOut = !empty($transaction->account_from) && empty($transaction->account_to);
                        $isIn = empty($transaction->account_from) && !empty($transaction->account_to);
                        $rowClass = $isOut ? 'bg-red-50/50' : ($isIn ? 'bg-green-50/50' : '');
                        $amountClass = $isOut ? 'text-red-700' : ($isIn ? 'text-green-700' : 'text-indigo-700');
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td class="px-3 py-2 text-sm text-gray-700">{{ $transaction->date->format('d M Y') }}</td>
                        <td class="px-3 py-2 text-sm text-gray-700">{{ ucfirst($transaction->type) }}</td>
                        <td class="px-3 py-2 text-sm text-gray-700">{{ $transaction->account_from ? ucfirst($transaction->account_from) : '-' }}</td>
                        <td class="px-3 py-2 text-sm text-gray-700">{{ $transaction->account_to ? ucfirst($transaction->account_to) : '-' }}</td>
                        <td class="px-3 py-2 text-sm font-semibold {{ $amountClass }}">
                            {{ $isOut ? '-' : ($isIn ? '+' : '') }}&#8377;{{ number_format((float) $transaction->amount, 2) }}
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-700">{{ $transaction->note ?: '-' }}</td>
                        <td class="px-3 py-2 text-sm">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('finance.transactions.edit', $transaction) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                                <button
                                    type="button"
                                    class="text-red-600 hover:text-red-800 openDeleteModal"
                                    data-no-loader
                                    data-action="{{ route('finance.transactions.destroy', $transaction) }}"
                                    data-label="{{ ucfirst($transaction->type) }} - &#8377;{{ number_format((float) $transaction->amount, 2) }}">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-3 py-4 text-center text-sm text-gray-500">No transactions found.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="4" class="px-3 py-2 text-sm font-semibold text-gray-800">Total (Filtered)</td>
                        <td class="px-3 py-2 text-sm font-semibold {{ $filteredSummary['net'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            &#8377;{{ number_format($filteredSummary['net'], 2) }}
                        </td>
                        <td class="px-3 py-2 text-xs text-gray-600">
                            In: &#8377;{{ number_format($filteredSummary['inflow'], 2) }} | Out: &#8377;{{ number_format($filteredSummary['outflow'], 2) }}
                        </td>
                        <td class="px-3 py-2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>

<div id="confirmModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[9999] p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-5">
        <h3 class="text-lg font-semibold text-gray-900" id="confirmTitle">Confirm Action</h3>
        <p class="text-sm text-gray-600 mt-2" id="confirmMessage"></p>
        <form id="confirmForm" method="post" class="mt-4 flex justify-end gap-2">
            @csrf
            <input type="hidden" name="confirm" value="1">
            <input type="hidden" name="_method" id="confirmMethod" value="POST">
            <button type="button" data-no-loader id="cancelModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Confirm</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('confirmModal');
    const title = document.getElementById('confirmTitle');
    const message = document.getElementById('confirmMessage');
    const form = document.getElementById('confirmForm');
    const methodField = document.getElementById('confirmMethod');

    const openModal = function (config) {
        title.textContent = config.title;
        message.textContent = config.message;
        form.action = config.action;
        methodField.value = config.method;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    const closeModal = function () {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    document.querySelectorAll('.openDeleteModal').forEach(function (button) {
        button.addEventListener('click', function () {
            const label = button.getAttribute('data-label') || 'this transaction';
            openModal({
                title: 'Delete Transaction',
                message: 'This will permanently delete ' + label + '. Continue?',
                action: button.getAttribute('data-action'),
                method: 'DELETE'
            });
        });
    });

    const clearAllButton = document.getElementById('openClearAllModal');
    if (clearAllButton) {
        clearAllButton.addEventListener('click', function () {
            openModal({
                title: 'Clear All Transactions',
                message: 'This will delete all transactions and reset opening cash/bank balances to zero. Continue?',
                action: '{{ route('finance.clear-all') }}',
                method: 'POST'
            });
        });
    }

    document.getElementById('cancelModal')?.addEventListener('click', closeModal);
    modal?.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });
});
</script>
@endsection
