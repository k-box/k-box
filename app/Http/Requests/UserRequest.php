<?php

namespace KlinkDMS\Http\Requests;

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
            'institution' => 'sometimes|required|exists:institutions,id',
        ];

        if ($action['as'] == 'administration.users.store') {
            $tests['email'] = 'required|email|unique:users,email';
            $tests['capabilities'] = 'required|array|exists:capabilities,key';
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
