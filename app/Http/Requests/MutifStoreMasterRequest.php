<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MutifStoreMasterRequest extends FormRequest
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
            'mutif_store_master' => 'required',
            'distributor_id' => 'required',
        ];
    }
}
