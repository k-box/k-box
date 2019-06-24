<?php

namespace KBox\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest as Request;

class AnalyticsSaveRequest extends Request
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
        $available_services = collect(config('analytics.services'))->keys()->implode(',');

        return [
            'analytics_token' => 'nullable|sometimes|string',
            'analytics_service' => 'nullable|sometimes|string|in:'.$available_services,
            'analytics_domain' => [
                'nullable','sometimes','string', Rule::requiredIf(function () {
                    return $this->input('analytics_token') && $this->input('analytics_service') === 'matomo';
                })
            ],
        ];
    }
}
