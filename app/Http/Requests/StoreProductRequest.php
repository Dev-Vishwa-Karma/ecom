<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
           'name'        => 'required|string|max:255',
           'description' => 'nullable|string',

        'images'   => 'required|array|min:1',
        'images.*' => 'image|max:5120',

        'variants'             => 'required|array|min:1',
        'variants.*.price'     => 'required|numeric|min:0',
        'variants.*.quantity'  => 'required|integer',
        'variants.*.color'     => 'nullable|string|max:50',
        'variants.*.size'      => 'nullable|string|max:50',
        'variants.*.gender'    => 'nullable|string|max:50',
        ];
    }
}
