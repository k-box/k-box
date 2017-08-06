<?php

namespace KlinkDMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class CreatePublicLinkRequest extends Request
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
            'to_id' => 'required|integer',
            'to_type' => 'required|string|in:document,collection',
            'expiration' => 'sometimes|filled|date|after:now', // if you put that field, it must be not empty
            'slug' => 'sometimes|filled|alpha_dash|max:250|unique:publiclinks,slug', // if you put that field, it must be not empty
        ];
    }
}
