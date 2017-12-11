<?php

namespace KBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class ContactsSaveRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check() && auth()->user()->isDMSManager();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|present|sometimes|string',
            'website' => 'nullable|present|sometimes|string|url',
            'image' => 'nullable|present|sometimes|string|url',
            'address_street' => 'nullable|present|sometimes|string',
            'address_locality' => 'nullable|present|sometimes|string',
            'address_country' => 'nullable|present|sometimes|string',
            'address_zip' => 'nullable|present|sometimes|string',
        ];
    }
}
