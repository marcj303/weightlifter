<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class FeedbackRequest extends Request
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
        return
        [
            'application_id' => "required|integer|exists:applications,id",
            'regarding_id' => "required|integer",
            'regarding_type' => "required|in:question,document",
            'question' => 'required|min:3',
            'type' => 'required|in:input,text,dropdown,boolean,file',
        ];
    }
}
