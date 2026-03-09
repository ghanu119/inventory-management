@extends('layouts.app')

@section('title', 'Edit Transaction')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Edit Transaction</h1>
        <p class="text-sm text-gray-600 mb-5">Update transaction details.</p>

        @include('finance._transaction-form', ['transaction' => $transaction, 'formMode' => $formMode])
    </div>
</div>
@endsection

