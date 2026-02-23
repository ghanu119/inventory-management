@extends('layouts.app')

@section('title', 'Truncate Customers')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Truncate Customers') }}</h1>
        <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">{{ __('Back to Customers') }}</a>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @if(! $canTruncate)
                <div class="mb-6">
                    <p class="text-red-700 font-medium mb-2">
                        {{ __('Truncate customers is not possible because invoices exist.') }}
                    </p>
                    <p class="text-gray-600 mb-4">
                        {{ __('There are :count invoice(s) in the system. Customers cannot be truncated until all invoices have been removed.', ['count' => $invoicesCount]) }}
                    </p>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">{{ __('How to truncate invoices first') }}</h2>
                        <ol class="list-decimal list-inside space-y-2 text-gray-700 text-sm">
                            <li>{{ __('Go to the app menu:') }} <strong>{{ __('Data') }}</strong> → <strong>{{ __('Truncate invoices & revert stock') }}</strong>.</li>
                            <li>{{ __('Optionally choose a date to truncate only invoices on or before that date (e.g. end of year), or leave empty to truncate all invoices.') }}</li>
                            <li>{{ __('Click "Update count" to see how many invoices will be removed.') }}</li>
                            <li>{{ __('Check the confirmation box and, if needed, "Also truncate stock history for all products" to clear stock history and set product stock to zero.') }}</li>
                            <li>{{ __('Click the red button to truncate. Invoices will be deleted and stock will be reverted (and optionally stock history cleared).') }}</li>
                            <li>{{ __('Return to this screen') }} (<a href="{{ route('customers.truncate') }}" class="text-indigo-600 hover:underline">{{ __('Truncate Customers') }}</a>) {{ __('via the menu') }} <strong>{{ __('Data') }}</strong> → <strong>{{ __('Truncate customers') }}</strong> {{ __('and you will then be able to truncate customers.') }}</li>
                        </ol>
                        <p class="mt-4 text-gray-600 text-sm">
                            {{ __('After you have truncated all invoices, this page will show a confirmation form to truncate all customers.') }}
                        </p>
                    </div>

                    <a href="{{ route('invoices.truncate') }}" class="inline-flex px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        {{ __('Go to Truncate Invoices') }}
                    </a>
                </div>
            @else
                <p class="text-gray-600 mb-4">
                    {{ __('This will permanently delete all :count customer(s). No invoices exist, so truncate is allowed.', ['count' => $customersCount]) }}
                </p>

                @if($customersCount > 0)
                    @php
                        $confirmMsg = __('This will permanently delete all :count customer(s). This cannot be undone. Continue?', ['count' => $customersCount]);
                    @endphp
                    <form method="post" action="{{ route('customers.truncate.store') }}" onsubmit="return confirm({{ json_encode($confirmMsg) }});">
                        @csrf
                        <input type="hidden" name="confirm" value="1">
                        <div class="flex items-center gap-2 mb-4">
                            <input type="checkbox" name="confirm_check" id="confirm_check" value="1" required class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="confirm_check" class="text-sm text-gray-700">
                                {{ __('I understand that all customers will be permanently removed.') }}
                            </label>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            {{ __('Truncate all :count customer(s)', ['count' => $customersCount]) }}
                        </button>
                    </form>
                @else
                    <p class="text-gray-500">{{ __('No customers to truncate.') }}</p>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
