<?php

namespace App\Http\Requests\Admin\Clothes;

use Illuminate\Foundation\Http\FormRequest;

class CreateClothesRequest extends FormRequest
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
            'entity_name' => 'required',
            'article_name' => 'required',
            'color' => 'required',
            'material' => 'required',
            'combo' => 'required',
            'special_feature' => 'required',
            'keyword' => 'required',
            'description' => 'required',
            'group_article' => 'required',
            'type_id' => 'required',
            'buffer_stock' => 'required|array',
            'partnumber' => 'required'
        ];
    }
}
