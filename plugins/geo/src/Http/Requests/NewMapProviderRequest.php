<?php

namespace KBox\Geo\Http\Requests;

use KBox\Geo\GeoService;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class NewMapProviderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return optional($this->user())->isDMSAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $service = app(GeoService::class);

        $current = collect($service->config('map')['providers'] ?? [])->pluck('label')->toArray();
        $defaults = collect($service->defaultConfig('map')['providers'] ?? [])->pluck('label')->toArray();
        
        return [
            'label' => ["required","string", Rule::notIn(array_merge($current, $defaults)),],
            'url' => ['required', 'string', 'regex:/https?:\/\/.+/'],
            'type' => 'required|string|filled|in:tile,wms',
            'maxZoom' => 'sometimes|integer',
            'layers' => 'bail|sometimes|nullable|required_if:type,wms|string',
            'subdomains' => 'sometimes|nullable|string',
            'attribution' => 'required|nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'label.not_in' => trans('geo::settings.validation.label.not_in', ['label' => $this->input('label')]),
            'url.regex' => trans('geo::settings.validation.url.regex'),
        ];
    }
}
