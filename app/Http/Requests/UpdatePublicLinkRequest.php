<?php

namespace KBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class UpdatePublicLinkRequest extends Request
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

        // get the current id to prevent that validation will fail because
        // the current slug was submitted again
        $id = $this->route('links');

        return [
            'expiration' => 'sometimes|filled|date|after:now', // if you put that field, it must be not empty
            'slug' => 'sometimes|filled|alpha_dash|max:250|unique:publiclinks,slug,'.$id.',slug', // if you put that field, it must be not empty
        ];
    }
}
