<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('products')->ignore($productId)],
            'hsn_code' => ['nullable', 'string', 'max:32'],
            'price' => ['required', 'numeric', 'min:0'],
            'gst_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_gst_included' => ['sometimes', 'boolean'],
            'warranty_years' => ['nullable', 'numeric', 'min:0'],
            'custom_short_text' => ['nullable', 'string'],
        ];
    }
}

