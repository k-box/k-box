<?php

namespace KBox\Http\Requests;

use KBox\Option;
use KBox\Capability;
use KBox\Rules\EnsureContainsAtLeast;
use Illuminate\Foundation\Http\FormRequest as Request;

class UserRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $action = $this->route()->getAction();

        $tests = [
            'name' => 'required|string',
        ];

        if ($action['as'] == 'administration.users.store') {
            $password_sending_available = Option::isMailEnabled() && ! Option::isMailUsingLogDriver();

            $tests['email'] = 'required|email|unique:users,email';
            $tests['capabilities'] = [
                'required',
                'array',
                'exists:capabilities,key',
                (new EnsureContainsAtLeast(Capability::$PARTNER))->setCustomMessage(trans('validation.custom.capabilities.ensure_contains_at_least'))
            ];
            $tests['password'] = 'sometimes|nullable|string|min:8';

            if ($password_sending_available && empty($this->input('password', null))) {
                $tests['send_password'] = 'required|boolean|accepted';
            } else {
                $tests['send_password'] = 'sometimes|nullable|boolean';
            }
        } else {
            $tests['capabilities'] = [
                'sometimes',
                'required',
                'array',
                'exists:capabilities,key',
                (new EnsureContainsAtLeast(Capability::$PARTNER))->setCustomMessage(trans('validation.custom.capabilities.ensure_contains_at_least'))
            ];
            $tests['email'] = 'required|email';
        }
        return $tests;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
