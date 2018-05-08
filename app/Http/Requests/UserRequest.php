<?php

namespace KBox\Http\Requests;

use KBox\Option;
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
            $tests['capabilities'] = 'required|array|exists:capabilities,key';

            if ($password_sending_available) {
                $tests['password'] = 'sometimes|nullable|required_without:generate_password|string|min:8';
                $tests['generate_password'] = 'required_without:password|boolean';
                $tests['send_password'] = 'sometimes|nullable|boolean|required_if:generate_password,1';
            } else {
                $tests['password'] = 'required|string|min:8';
            }
        } else {
            $tests['capabilities'] = 'sometimes|required|array|exists:capabilities,key';
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
