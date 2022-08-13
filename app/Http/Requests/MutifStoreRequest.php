<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MutifStoreRequest extends FormRequest
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
            'ms_ms_name' => 'required',
            'ms_code' => 'required',
            'open_date' => 'required',
            'status' => 'required',
            'msdp' => 'required',
            'address' => 'required',
            'province' => 'required',
            'regency' => 'required',
            'district' => 'required',
            'phone' => 'required',
            'fax' => 'required',
            'addr_type' => 'required',
            'zip' => 'required',
        ];
    }
}
