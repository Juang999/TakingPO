<?php

namespace App\Http\Requests\Client\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ClientRegisterRequest extends FormRequest
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
            'phone_1' => 'required|unique:distributors,phone',
            'distributor_id' => 'nullable',
            'partner_group_id' => 'required',
            'address' => 'required',
            'province' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'open_date' => 'nullable',
            'status' => 'nullable'
        ];
    }
}
