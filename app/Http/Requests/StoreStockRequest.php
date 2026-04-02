<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id', 
            'color'    => 'nullable|string|max:50',
            'size'     => 'nullable|string|max:50',
            'gender'   => 'nullable|string|max:50',
            'price'    => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        
        ];
    }
}
