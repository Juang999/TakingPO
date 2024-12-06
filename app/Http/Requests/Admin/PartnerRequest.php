<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PartnerRequest extends FormRequest
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
            'phone' => 'required|integer|min:10',
            'parent_id' => 'nullable',
            'group_code' => 'required',
            'partner_group_id' => 'required',
            'level' => 'required',
            'training_level' => 'required',
        ];
    }
}
