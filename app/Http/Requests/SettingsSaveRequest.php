<?php

namespace KBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class SettingsSaveRequest extends Request
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
            'public_core_enabled' => 'nullable|sometimes|required|in:true',
            'public_core_url' => 'nullable|sometimes|required|url',
            'public_core_password' => 'nullable|sometimes|required_with:public_core_url|string',
            'public_core_network_name_en' => 'nullable|sometimes|string',
            'public_core_network_name_ru' => 'nullable|sometimes|string',
            'streaming_service_url' => 'nullable|sometimes|string|url',
        ];
    }
}
