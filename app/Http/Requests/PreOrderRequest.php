<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreOrderRequest extends FormRequest
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
            'clothes_id' => 'required|integer',
            'info' => 'required',
            'veil' => 'required',
            'size_s' => 'required|integer',
            'size_m' => 'required|integer',
            'size_l' => 'required|integer',
            'size_xl' => 'required|integer',
            'size_xxl' => 'required|integer',
            'size_xxxl' => 'required|integer',
            'size_2' => 'required|integer',
            'size_4' => 'required|integer',
            'size_6' => 'required|integer',
            'size_8' => 'required|integer',
            'size_10' => 'required|integer',
            'size_12' => 'required|integer',
            'size_27' => 'required|integer',
            'size_28' => 'required|integer',
            'size_29' => 'required|integer',
            'size_30' => 'required|integer',
            'size_31' => 'required|integer',
            'size_32' => 'required|integer',
            'size_33' => 'required|integer',
            'size_34' => 'required|integer',
            'size_35' => 'required|integer',
            'size_36' => 'required|integer',
            'size_37' => 'required|integer',
            'size_38' => 'required|integer',
            'size_39' => 'required|integer',
            'size_40' => 'required|integer',
            'size_41' => 'required|integer',
            'size_42' => 'required|integer',
            'other' => 'required|integer',
            'total' => 'required'
        ];
    }
}
