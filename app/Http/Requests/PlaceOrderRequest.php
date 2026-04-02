<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'variant_id'     => 'required|exists:product_variants,id',
            'quantity'       => 'required|integer|min:1',
            'customer_name'  => 'required|string|max:255',
            'address'        => 'required|string',
            'mobile'         => 'required|string|max:15',
            'email'          => 'required|email',
            'payment_mode'   => 'required|in:cod,online',
            'card_number'    => 'required_if:payment_mode,online|nullable|string|size:16',
            'card_cvv'       => 'required_if:payment_mode,online|nullable|string|size:3',
            'card_expiry'    => 'required_if:payment_mode,online|nullable|string|size:5',
            'declaration'    => 'required|accepted',
            'total_price'    => 'required|numeric|min:0',
        ];
    }

}
