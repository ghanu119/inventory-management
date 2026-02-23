@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Add Customer</h1>

    <form action="{{ route('customers.store') }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf

        <div class="space-y-5">
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Enter customer name">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Enter phone number">
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Enter email address">
                </div>
            </div>

            <div>
                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                <textarea name="address" id="address" rows="3" placeholder="Enter customer address">{{ old('address') }}</textarea>
            </div>

            <div>
                <label for="gst_number" class="block text-sm font-semibold text-gray-700 mb-2">GST Number</label>
                <input type="text" name="gst_number" id="gst_number" value="{{ old('gst_number') }}" placeholder="Enter GST number">
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('customers.index') }}" class="px-4 py-2 border border-gray-300 rounded-md">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Save</button>
            </div>
        </div>
    </form>
</div>
@endsection
