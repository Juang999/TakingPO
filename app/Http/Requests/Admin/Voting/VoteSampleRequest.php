<?php

namespace App\Http\Requests\Admin\Voting;

use Illuminate\Foundation\Http\FormRequest;

class VoteSampleRequest extends FormRequest
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
            'voting_event_id' => 'required|integer',
            'sample_product_id' => 'required|integer',
            'attendance_id' => 'required|integer',
            'score' => 'required|integer',
            'note' => 'required|integer',
        ];
    }
}
