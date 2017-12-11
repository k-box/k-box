<?php

namespace KBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use KBox\User;

class UserOptionUpdateRequest extends Request
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
            User::OPTION_LIST_TYPE => 'sometimes|required|in:details,tiles,cards',
            User::OPTION_LANGUAGE => 'sometimes|required|in:en,ru',
            User::OPTION_TERMS_ACCEPTED => 'sometimes|required|boolean'
        ];
    }
}
