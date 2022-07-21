<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SingleAgentRequest extends FormRequest
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
            'distributor_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'phone_2' => 'required',
            'partner_group_id' => 'required',
            'address' => 'required',
            'fax_1' => 'required',
            'district' => 'required',
            'regency' => 'required',
            'province' => 'required',
            'addr_type' => 'required',
            'zip' => 'required',
            'comment' => 'required',
            'ms_ms_name' => 'required',
            'ms_code' => 'required',
            'open_date' => 'required',
            'status' => 'required',
            'msdp' => 'required',
            
        ];
    }
}
