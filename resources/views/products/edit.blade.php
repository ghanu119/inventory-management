@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Product</h1>

    <form action="{{ route('products.update', $product) }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="space-y-5">
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required placeholder="Enter product name">
            </div>

            <div>
                <label for="sku" class="block text-sm font-semibold text-gray-700 mb-2">SKU <span class="text-red-500">*</span></label>
                <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" required placeholder="Enter SKU code">
            </div>

            <div>
                <label for="hsn_code" class="block text-sm font-semibold text-gray-700 mb-2">HSN Code</label>
                <input type="text" name="hsn_code" id="hsn_code" value="{{ old('hsn_code', $product->hsn_code) }}" placeholder="Enter HSN code">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Price <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $product->price) }}" required min="0" placeholder="0.00">
                    <p class="mt-1 text-xs text-gray-500">If "Price includes GST" is checked, enter GST-inclusive price, otherwise enter price without GST.</p>
                </div>

                <div>
                    <label for="gst_rate" class="block text-sm font-semibold text-gray-700 mb-2">GST Rate (%) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="gst_rate" id="gst_rate" value="{{ old('gst_rate', $product->gst_rate) }}" required min="0" max="100" placeholder="0.00">
                </div>
            </div>

            <div>
                <label class="inline-flex items-center text-sm font-semibold text-gray-700">
                    <input
                        type="checkbox"
                        name="is_gst_included"
                        id="is_gst_included"
                        value="1"
                        class="mr-2"
                        {{ old('is_gst_included', $product->is_gst_included) ? 'checked' : '' }}
                    >
                    Price includes GST
                </label>
            </div>

            <div>
                <label for="warranty_years" class="block text-sm font-semibold text-gray-700 mb-2">Year of warranty</label>
                <input type="number" step="0.01" name="warranty_years" id="warranty_years" value="{{ old('warranty_years', $product->warranty_years) }}" min="0" placeholder="e.g. 1 or 1.5">
            </div>

            <div>
                <label for="custom_short_text" class="block text-sm font-semibold text-gray-700 mb-2">Custom short text for product</label>
                <textarea name="custom_short_text" id="custom_short_text" rows="3" placeholder="Optional short description or note (multiline)">{{ old('custom_short_text', $product->custom_short_text) }}</textarea>
            </div>

            <!-- Stock quantity is managed from the Stock Management screen -->

            <div class="flex justify-end space-x-3">
                <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-300 rounded-md">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update</button>
            </div>
        </div>
    </form>
</div>
@endsection
