@extends('layouts.app')

@section('title', 'Truncate Invoices')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Truncate Invoices') }}</h1>
        <a href="{{ route('invoices.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">{{ __('Back to Invoices') }}</a>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <p class="text-gray-600 mb-4">
                {{ __('Use this to clear invoice data (e.g. at end of year). All selected invoices will be permanently deleted and stock will be reverted: quantities will be added back to products and serial numbers will be set to available again.') }}
            </p>

            <form method="get" action="{{ route('invoices.truncate') }}" class="mb-6 flex flex-wrap items-end gap-4">
                <div>
                    <label for="before_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Truncate invoices on or before (leave empty for all)') }}</label>
                    <input type="date" name="before_date" id="before_date" value="{{ old('before_date', $beforeDate) }}" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">{{ __('Update count') }}</button>
            </form>

            <p class="text-sm font-medium text-gray-700 mb-2">
                {{ __('Invoices that will be truncated:') }} <strong>{{ $invoicesCount }}</strong>
            </p>

            @if($invoicesCount > 0)
                @php
                    $confirmTruncateMsg = __('This will permanently delete :count invoice(s) and revert stock. This cannot be undone. Continue?', ['count' => $invoicesCount]);
                    $confirmTruncateWithHistoryMsg = __('This will permanently delete :count invoice(s), revert stock, and clear all stock history for every product. This cannot be undone. Continue?', ['count' => $invoicesCount]);
                @endphp
                <form method="post" action="{{ route('invoices.truncate.store') }}" id="truncate-form" onsubmit="var msg = document.getElementById('truncate_stock_history').checked ? {{ json_encode($confirmTruncateWithHistoryMsg) }} : {{ json_encode($confirmTruncateMsg) }}; return confirm(msg);">
                    @csrf
                    @if($beforeDate)
                        <input type="hidden" name="before_date" value="{{ $beforeDate }}">
                    @endif
                    <input type="hidden" name="confirm" value="1">
                    <div class="space-y-3 mb-4">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="confirm_check" id="confirm_check" value="1" required class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="confirm_check" class="text-sm text-gray-700">
                                {{ __('I understand that the selected invoices will be permanently removed and stock entries will be reverted.') }}
                            </label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="truncate_stock_history" id="truncate_stock_history" value="1" {{ old('truncate_stock_history') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="truncate_stock_history" class="text-sm text-gray-700">
                                {{ __('Also truncate stock history for all products') }}
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 ml-6">
                            {{ __('If selected, all stock history and all serial numbers will be removed, and current stock will be set to zero for every product. New stock-in entries will create new serials; the invoice serial dropdown will then show only those.') }}
                        </p>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        {{ __('Truncate :count invoice(s)', ['count' => $invoicesCount]) }}
                    </button>
                </form>
            @else
                <p class="text-gray-500 mb-4">{{ __('No invoices to truncate.') }}</p>
                <p class="text-sm text-gray-600 mb-2">{{ __('If you previously truncated invoices with stock history but the invoice serial dropdown still shows old serial numbers, clear stock history and serials now:') }}</p>
                <form method="post" action="{{ route('invoices.clear-history-and-serials') }}" onsubmit="return confirm({{ json_encode(__('This will clear all stock history and serial numbers and set product stock to zero. New stock-in entries will create new serials. Continue?')) }});">
                    @csrf
                    <input type="hidden" name="confirm" value="1">
                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700">{{ __('Clear stock history and serial numbers') }}</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
