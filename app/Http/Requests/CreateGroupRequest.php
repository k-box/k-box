<?php

namespace KBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class CreateGroupRequest extends Request
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
            'name' => 'required|regex:/^[\w\d\s\.\-_\(\)]*/',
            'color' => 'sometimes|required|alpha_num|size:6',
            'parent' => 'sometimes|required|integer|exists:groups,id',
            'public' => 'sometimes|required_with_all:parent|boolean',
        ];
    }
}
