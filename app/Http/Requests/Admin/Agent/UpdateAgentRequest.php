<?php

namespace App\Http\Requests\Admin\Agent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgentRequest extends FormRequest
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
            'name' => 'nullable',
            'phone' => 'nullable',
            'partner_group_id' => 'nullable',
            'ms_ms_name' => 'nullable',
            'ms_code' => 'nullable',
            'partner_group_id' => 'nullable',
            'partner_group_id' => 'nullable',
            'distributor_id' => 'nullable',
            'open_date' => 'nullable',
            'status' => 'nullable',
            'msdp' => 'nullable',
            'url' => 'nullable',
            'remarks' => 'nullable',
            'address' => 'nullable',
            'province' => 'nullable',
            'regency' => 'nullable',
            'district' => 'nullable',
            'phone_1' => 'nullable',
            'fax_1' => 'nullable',
            'addr_type' => 'nullable',
            'zip' => 'nullable',
            'comment' => 'nullable',
        ];
    }
}
