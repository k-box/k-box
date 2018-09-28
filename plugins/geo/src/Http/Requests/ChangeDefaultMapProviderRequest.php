<?php

namespace KBox\Geo\Http\Requests;

use KBox\Geo\GeoService;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ChangeDefaultMapProviderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return optional($this->user())->isDMSAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $service = app(GeoService::class);

        $mapIds = collect($service->config('map')['providers'] ?? [])->keys()->toArray();
        
        return [
            'default' => ["required","string", Rule::in($mapIds),],
        ];
    }

    public function messages()
    {
        return [
            'default.in' => trans('geo::settings.validation.default_map.id'),
        ];
    }
}
