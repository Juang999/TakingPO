<?php

namespace App\Http\Requests\Admin\Distributor;

use Illuminate\Foundation\Http\FormRequest;

class DistributorRequest extends FormRequest
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
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'level' => 'required',
            'province' => 'required',
            'regency' => 'required',
            'district' => 'required',
            'addr_type' => 'required',
            'zip' => 'required'
        ];
    }
}
