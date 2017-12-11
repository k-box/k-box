<?php

namespace KBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class ProjectRequest extends Request
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
            'description' => 'nullable|sometimes|string',
            'users' => 'required|array|exists:users,id',
            'manager' => 'required|exists:users,id',
            'avatar' => 'nullable|sometimes|required|image|max:200'
        ];

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
