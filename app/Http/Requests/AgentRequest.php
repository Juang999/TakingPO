<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgentRequest extends FormRequest
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
            'db_name' => 'required',
            'db_level' => 'required',
            'role' => 'required',
            'ms_name' => 'required',
            'ms_ms_name' => 'required',
            'ms_code' => 'required',
            'ms_training_level' => 'required',
            'ms_address' => 'required',
            'ms_district' => 'required',
            'ns_regency' => 'required',
            'ms_provice' => 'required',
            'ms_open_date' => 'required',
            'ns_phone' => 'required',
            'ms_status' => 'required',
            'ms_msdp'=> 'required',
        ];
    }
}
