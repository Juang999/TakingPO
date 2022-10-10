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
            'size_27' => 'required',
            'bs_size_27' => 'required',
            'size_28' => 'required',
            'bs_size_28' => 'required',
            'size_29' => 'required',
            'bs_size_29' => 'required',
            'size_30' => 'required',
            'bs_size_30' => 'required',
            'size_31' => 'required',
            'bs_size_31' => 'required',
            'size_32' => 'required',
            'bs_size_32' => 'required',
            'size_33' => 'required',
            'bs_size_33' => 'required',
            'size_34' => 'required',
            'bs_size_34' => 'required',
            'size_35' => 'required',
            'bs_size_35' => 'required',
            'size_36' => 'required',
            'bs_size_36' => 'required',
            'size_37' => 'required',
            'bs_size_37' => 'required',
            'size_38' => 'required',
            'bs_size_38' => 'required',
            'size_39' => 'required',
            'bs_size_39' => 'required',
            'size_40' => 'required',
            'bs_size_40' => 'required',
            'size_41' => 'required',
            'bs_size_41' => 'required',
            'size_42' => 'required',
            'bs_size_42' => 'required',
            'other' => 'required',
            'bs_other' => 'required',
            'category' => 'required'
        ];
    }
}
