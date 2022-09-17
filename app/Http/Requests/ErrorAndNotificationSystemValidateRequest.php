<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ErrorAndNotificationSystemValidateRequest extends FormRequest
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
            'notify_code'              => 'required|regex:/^[a-zA-Z0-9_ ]+$/u|max:10',
            'notify_short_description' => 'required|max:255',
        ];
    }
}
