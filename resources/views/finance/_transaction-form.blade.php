@php
    $isEdit = $formMode === 'edit';
    $submitRoute = $isEdit ? route('finance.transactions.update', $transaction) : route('finance.transactions.store');
    $submitText = $isEdit ? 'Update Transaction' : 'Add Transaction';
@endphp

<form method="post" action="{{ $submitRoute }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div>
        <label for="txn_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
        <input type="date" id="txn_date" name="date" value="{{ old('date', optional($transaction->date)->format('Y-m-d') ?? $transaction->date ?? now()->toDateString()) }}" required>
    </div>

    <div>
        <label for="txn_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
        <select id="txn_type" name="type" class="select2" required>
            <option value="income" @selected(old('type', $transaction->type) === 'income')>Income</option>
            <option value="expense" @selected(old('type', $transaction->type) === 'expense')>Expense</option>
            <option value="transfer" @selected(old('type', $transaction->type) === 'transfer')>Transfer</option>
        </select>
    </div>

    <div>
        <label for="txn_amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
        <input type="number" step="0.01" min="0.01" id="txn_amount" name="amount" value="{{ old('amount', $transaction->amount) }}" placeholder="0.00" required>
    </div>

    <div>
        <label for="txn_account_from" class="block text-sm font-medium text-gray-700 mb-1">Account From</label>
        <select id="txn_account_from" name="account_from" class="select2">
            <option value="">Select</option>
            <option value="cash" @selected(old('account_from', $transaction->account_from) === 'cash')>Cash</option>
            <option value="bank" @selected(old('account_from', $transaction->account_from) === 'bank')>Bank</option>
        </select>
    </div>

    <div>
        <label for="txn_account_to" class="block text-sm font-medium text-gray-700 mb-1">Account To</label>
        <select id="txn_account_to" name="account_to" class="select2">
            <option value="">Select</option>
            <option value="cash" @selected(old('account_to', $transaction->account_to) === 'cash')>Cash</option>
            <option value="bank" @selected(old('account_to', $transaction->account_to) === 'bank')>Bank</option>
        </select>
    </div>

    <div class="md:col-span-2">
        <label for="txn_note" class="block text-sm font-medium text-gray-700 mb-1">Note</label>
        <input type="text" id="txn_note" name="note" value="{{ old('note', $transaction->note) }}" placeholder="Optional note">
    </div>

    <div class="md:col-span-2 flex justify-end gap-2">
        <a href="{{ route('finance.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-100">Cancel</a>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">{{ $submitText }}</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('txn_type');
    const accountFrom = document.getElementById('txn_account_from');
    const accountTo = document.getElementById('txn_account_to');

    const setSelectDisabled = function (selectElement, disabled) {
        selectElement.disabled = disabled;
        if (window.jQuery && jQuery().select2) {
            $(selectElement).prop('disabled', disabled).trigger('change.select2');
        }
    };

    const syncAccountFields = function () {
        const type = typeSelect.value;

        setSelectDisabled(accountFrom, false);
        setSelectDisabled(accountTo, false);
        accountFrom.required = false;
        accountTo.required = false;

        if (type === 'income') {
            accountFrom.value = '';
            setSelectDisabled(accountFrom, true);
            accountTo.required = true;
        } else if (type === 'expense') {
            accountTo.value = '';
            setSelectDisabled(accountTo, true);
            accountFrom.required = true;
        } else {
            accountFrom.required = true;
            accountTo.required = true;
        }
    };

    typeSelect.addEventListener('change', syncAccountFields);
    if (window.jQuery && jQuery().select2) {
        $(typeSelect).on('change select2:select', syncAccountFields);
    }
    syncAccountFields();
});
</script>
