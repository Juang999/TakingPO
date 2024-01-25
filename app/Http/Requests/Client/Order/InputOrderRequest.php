<?php

namespace App\Http\Requests\Client\Order;

use Illuminate\Foundation\Http\FormRequest;

class InputOrderRequest extends FormRequest
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
            'event_id' => 'required',
            'product_id' => 'required',
            'S' => 'nullable|integer',
            'M' => 'nullable|integer',
            'L' => 'nullable|integer',
            'XL' => 'nullable|integer',
            'XXL' => 'nullable|integer',
            'XXXL' => 'nullable|integer',
            '2' => 'nullable|integer',
            '4' => 'nullable|integer',
            '6' => 'nullable|integer',
            '8' => 'nullable|integer',
            '10' => 'nullable|integer',
            '12' => 'nullable|integer',
            '27' => 'nullable|integer',
            '28' => 'nullable|integer',
            '29' => 'nullable|integer',
            '30' => 'nullable|integer',
            '31' => 'nullable|integer',
            '32' => 'nullable|integer',
            '33' => 'nullable|integer',
            '34' => 'nullable|integer',
            '35' => 'nullable|integer',
            '36' => 'nullable|integer',
            '37' => 'nullable|integer',
            '38' => 'nullable|integer',
            '39' => 'nullable|integer',
            '40' => 'nullable|integer',
            '41' => 'nullable|integer',
            '42' => 'nullable|integer',
            'other' => 'nullable|integer',
        ];
    }
}
