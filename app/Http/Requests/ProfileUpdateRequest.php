<?php

namespace KBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use KBox\User;

class ProfileUpdateRequest extends Request
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
            'name' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|unique:users,email',
            'password' => 'sometimes|required|min:8|regex:[\S]',
            'password_confirm' => 'required_with:password|same:password',
            'organization_name' => 'sometimes|nullable|string',
            'organization_website' => 'sometimes|nullable|url',
            '_change' => 'required|in:pass,mail,info,language',
            User::OPTION_LANGUAGE => 'sometimes|required|in:en,ru,tg'
        ];
    }
}
