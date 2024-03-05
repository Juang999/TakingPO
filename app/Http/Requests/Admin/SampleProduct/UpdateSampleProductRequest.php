<?php

namespace App\Http\Requests\Admin\SampleProduct;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSampleProductRequest extends FormRequest
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
            'date' => 'nullable|date',
            'article_name' => 'nullable|string',
            'entity_name' => 'nullable|string',
            'material' => 'nullable|string',
            'size' => 'nullable|string',
            'accessories' => 'nullable|string',
            'designer_id' => 'nullable|integer',
            'md_id' => 'nullable|integer',
            'leader_designer_id' => 'nullable|integer',
            'photo' => 'nullable|string'
        ];
    }
}
