<?php

namespace KlinkDMS\Http\Requests;

use KlinkDMS\Http\Requests\Request;

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
            'phone' => 'present|sometimes|string',
            'website' => 'present|sometimes|string|url',
            'image' => 'present|sometimes|string|url',
			'address_street' => 'present|sometimes|string',
			'address_locality' => 'present|sometimes|string',
			'address_country' => 'present|sometimes|string',
			'address_zip' => 'present|sometimes|string',
        ];
    }
}
