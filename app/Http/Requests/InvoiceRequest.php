<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'order' => 'required|exists:orders,id' // match GET param
        ];
    }

    public function validationData()
    {
        // get order_id from route param
        return $this->route()->parameters();
    }
}