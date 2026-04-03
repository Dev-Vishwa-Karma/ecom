<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRatingRequest extends FormRequest
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
           'product_id'   => 'required|exists:products,id',
        'variant_id'   => 'nullable|exists:product_variants,id',
        'rating'       => 'required|integer|min:1|max:5',
        'comment'      => 'nullable|string|max:1000',
        'post_sharing' => 'nullable|in:Google,Facebook',
        'posturl'      => 'nullable|url|max:255',
        ];
    }
}
