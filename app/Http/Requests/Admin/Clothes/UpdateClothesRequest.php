<?php

namespace App\Http\Requests\Admin\Clothes;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClothesRequest extends FormRequest
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
            'entity_name' => 'nullable',
            'article_name' => 'nullable',
            'color' => 'nullable',
            'material' => 'nullable',
            'combo' => 'nullable',
            'special_feature' => 'nullable',
            'keyword' => 'nullable',
            'description' => 'nullable',
            'group_article' => 'nullable',
            'type_id' => 'nullable',
            'is_active' => 'nullable',
            'price' => 'nullable',
            'qty' => 'nullable'
        ];
    }
}
