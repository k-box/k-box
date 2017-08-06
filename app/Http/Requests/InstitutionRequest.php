<?php

namespace KlinkDMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class InstitutionRequest extends Request
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
            'email' => 'required|email',
            'phone' => 'required|string',
            'url' => 'required|string',
            'thumbnail_uri' => 'required|string',
            'address_street' => 'sometimes|string',
            'address_locality' => 'sometimes|string',
            'address_country' => 'sometimes|string',
            'address_zip' => 'sometimes|string',
        ];

        if ($action['as'] == 'administration.institutions.store') {
            $tests['klink_id'] = 'required|alpha_num|unique:institutions,klink_id';
        } else {
            $tests['klink_id'] = 'required|alpha_num|exists:institutions,klink_id';
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
