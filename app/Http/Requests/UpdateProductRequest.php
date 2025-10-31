<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product')->id;
        $shopId = $this->user()->shop_id;

        return [
            'name' => 'sometimes|required|string|max:255',
            'sku' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('products', 'sku')
                    ->ignore($productId)
                    ->where('shop_id', $shopId),
            ],
            'price' => 'sometimes|required|numeric|min:0.01',
            'stock' => 'sometimes|required|integer|min:0',
        ];
    }
}
