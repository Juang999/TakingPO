<?php

namespace App\Http\Requests\Admin\SampleProduct;

use Illuminate\Foundation\Http\FormRequest;

class SampleProductRequest extends FormRequest
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
            'date' => 'required|date',
            'article_name' => 'required|string',
            'entity_name' => 'required|string',
            'style_id' => 'required|integer',
            'material' => 'required|string',
            'size' => 'required|string',
            'accessories' => 'required|string',
            'note_description' => 'nullable|string',
            'design_file' => 'nullable|string',
            'designer_id' => 'nullable|integer',
            'md_id' => 'nullable|integer',
            'leader_designer_id' => 'nullable|integer',
            'photo' => 'required|string',
            'sample_design' => 'required|string',
            'description_fabric' => 'required|string',
            'photo_fabric' => 'required|string'
        ];
    }
}
