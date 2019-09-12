<?php

namespace KBox\Http\Requests;

use Illuminate\Validation\Rule;
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

        $is_update = $action['as'] == 'projects.update';

        $name_unique_rule = Rule::unique('projects', 'name');

        $tests = [
            'name' => [
                'required',
                'string',
                $is_update ? $name_unique_rule->ignore($this->route('project')) : $name_unique_rule,
            ],
            'description' => 'nullable|sometimes|string',
            'users' => 'nullable|sometimes|required|array|exists:users,id',
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

    public function messages()
    {
        return [
            'name.unique' => trans('projects.errors.already_existing'),
        ];
    }
}
