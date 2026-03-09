@extends('layouts.app')

@section('title', 'Company Settings')

@section('content')
<div class="max-w-3xl">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Company Settings</h1>

    <form action="{{ route('company.update') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Company Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" required placeholder="Enter company name">
            </div>

            <div>
                <label for="gst_number" class="block text-sm font-semibold text-gray-700 mb-2">GST Number</label>
                <input type="text" name="gst_number" id="gst_number" value="{{ old('gst_number', $company->gst_number) }}" placeholder="Enter GST number">
            </div>

            <div>
                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                <textarea name="address" id="address" rows="3" placeholder="Enter company address">{{ old('address', $company->address) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $company->phone) }}" placeholder="Enter phone number">
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $company->email) }}" placeholder="Enter email address">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="opening_cash_balance" class="block text-sm font-semibold text-gray-700 mb-2">Opening Cash Balance</label>
                    <input type="number" step="0.01" min="0" name="opening_cash_balance" id="opening_cash_balance" value="{{ old('opening_cash_balance', $company->opening_cash_balance ?? 0) }}" placeholder="0.00">
                </div>
                <div>
                    <label for="opening_bank_balance" class="block text-sm font-semibold text-gray-700 mb-2">Opening Bank Balance</label>
                    <input type="number" step="0.01" min="0" name="opening_bank_balance" id="opening_bank_balance" value="{{ old('opening_bank_balance', $company->opening_bank_balance ?? 0) }}" placeholder="0.00">
                </div>
            </div>

            <div>
                <label for="logo" class="block text-sm font-semibold text-gray-700 mb-2">Company Logo</label>
                @if($company->getFirstMediaUrl('logo'))
                    <div class="mb-3">
                        <img src="{{ $company->getFirstMediaUrl('logo') }}" alt="Logo" class="h-20 w-auto rounded-lg border-2 border-gray-200">
                    </div>
                @endif
                <input type="file" name="logo" id="logo" accept="image/*" class="file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer cursor-pointer">
            </div>

            <div>
                <label for="invoice_terms_and_conditions" class="block text-sm font-semibold text-gray-700 mb-2">Invoice Terms and Conditions</label>
                <p class="text-xs text-gray-500 mb-1">Shown at the bottom of invoice PDF. Supports rich text (e.g. Gujarati).</p>
                <textarea name="invoice_terms_and_conditions" id="invoice_terms_and_conditions" class="hidden">{{ old('invoice_terms_and_conditions', $company->invoice_terms_and_conditions) }}</textarea>
                <div id="quill-terms" class="min-h-[200px] rounded-lg border border-gray-300 bg-white"></div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Save Settings</button>
            </div>
        </div>
    </form>
</div>

<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var textarea = document.getElementById('invoice_terms_and_conditions');
    var quillEl = document.getElementById('quill-terms');
    if (!textarea || !quillEl) return;

    var quill = new Quill('#quill-terms', {
        theme: 'snow',
        placeholder: 'Enter terms and conditions (e.g. warranty, return policy)...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }, { 'list': 'ordered'}, { 'list': 'bullet' }],
                ['bold', 'italic', 'underline', 'color'],
                [{ 'color': [] }, { 'background': [] }],
                ['clean']
            ]
        }
    });

    if (textarea.value) {
        quill.root.innerHTML = textarea.value;
    }

    function syncQuillToTextarea() {
        textarea.value = quill.root.innerHTML;
    }

    quill.on('text-change', syncQuillToTextarea);
    quill.root.addEventListener('blur', syncQuillToTextarea);

    var form = textarea.closest('form');
    if (form) {
        form.addEventListener('submit', function() {
            syncQuillToTextarea();
        }, false);
    }
});
</script>
@endsection
