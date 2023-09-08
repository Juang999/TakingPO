<?php

namespace App\Http\Requests\Partnumber;

use Illuminate\Foundation\Http\FormRequest;

class CreatePartnumberRequest extends FormRequest
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
            'clothes_id' => 'required',
            'image_id' => 'required',
            'partnumber' => 'required',
        ];
    }
}
