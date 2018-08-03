<?php

namespace KBox\Geo\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeoServerSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isDMSAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'geoserver_url' => 'required|string|url',
            'geoserver_username' => 'required|string|',
            'geoserver_password' => 'required|string|',
            'geoserver_workspace' => 'required|string|',
        ];
    }
}
