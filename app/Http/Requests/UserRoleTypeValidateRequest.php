<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRoleTypeValidateRequest extends FormRequest
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
            'rules' => [
                // TODO: check and validate requests
                // 'user_role_name'        => 'required|regex:/^[a-zA-Z_ ]+$/u|max:255',
                // 'user_role_description' => 'required',
                // 'user_role_is_active'   => 'required',
            ],
        ];
    }
}
