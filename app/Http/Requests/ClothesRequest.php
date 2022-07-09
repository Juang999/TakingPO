<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClothesRequest extends FormRequest
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
            "entity_name" => 'required',
            'article_name' => 'required',
            "color" => 'required',
            "material" => 'required',
            "combo" => 'required',
            "special_feature" => 'required',
            "keyword" => 'required',
            "description" => 'required',
            'group_article' => 'required',
            'type' => 'required',
            'size_s' => 'required',
            'bs_size_s' => 'required',
            'size_m' => 'required',
            'bs_size_m' => 'required',
            'size_l' => 'required',
            'bs_size_l' => 'required',
            'size_xl' => 'required',
            'bs_size_xl' => 'required',
            'size_xxl' => 'required',
            'bs_size_xxl' => 'required',
            'size_xxxl' => 'required',
            'bs_size_xxxl' => 'required',
            'size_2' => 'required',
            'bs_size_2' => 'required',
            'size_4' => 'required',
            'bs_size_4' => 'required',
            'size_6' => 'required',
            'bs_size_6' => 'required',
            'size_8' => 'required',
            'bs_size_8' => 'required',
            'size_10' => 'required',
            'bs_size_10' => 'required',
            'size_12' => 'required',
            'bs_size_12' => 'required',
        ];
    }
}
