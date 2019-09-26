<?php

namespace KBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Rule;
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
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|'.Rule::unique('users', 'name')->ignore($this->user()->id),
            'organization_name' => 'sometimes|nullable|string',
            'organization_website' => 'sometimes|nullable|url',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => trans('profile.errors.username_already_taken'),
        ];
    }
}
