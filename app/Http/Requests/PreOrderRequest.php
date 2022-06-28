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
        ];
    }
}
