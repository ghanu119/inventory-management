<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_date' => ['required', 'date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_address' => ['nullable', 'string'],
            'customer_gst_number' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.serial_no' => ['nullable', 'string', 'max:100'],
            'items.*.warranty_years' => ['nullable', 'numeric', 'min:0'],
            'items.*.custom_short_text' => ['nullable', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_type' => ['nullable', 'in:flat,percentage'],
            'payment_mode' => ['required', 'in:Cash,UPI,Card,Bank'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
