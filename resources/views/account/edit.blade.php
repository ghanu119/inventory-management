@extends('layouts.app')

@section('title', 'Account Settings')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Account Settings</h1>

    <form action="{{ route('account.update') }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="space-y-5">
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    placeholder="Enter your email"
                >
            </div>

            <div class="border-t pt-5">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Change Password</h2>
                <p class="text-sm text-gray-500 mb-4">Leave password fields blank if you only want to change email.</p>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Enter new password"
                        >
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="Confirm new password"
                        >
                    </div>
                </div>
            </div>

            <div class="border-t pt-5">
                <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">
                    Current Password <span class="text-red-500">*</span>
                </label>
                <input
                    type="password"
                    name="current_password"
                    id="current_password"
                    required
                    placeholder="Enter current password to save changes"
                >
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 border border-gray-300 rounded-md">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update Account</button>
            </div>
        </div>
    </form>
</div>
@endsection

