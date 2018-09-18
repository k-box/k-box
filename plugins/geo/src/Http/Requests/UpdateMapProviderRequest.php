<?php

namespace KBox\Geo\Http\Requests;

use KBox\Geo\GeoService;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMapProviderRequest extends FormRequest
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
        $providers = $service->config('map')['providers'];
        $provider = $providers[$this->route('id')];

        $currentLabel = $provider['label'];
        $type = $provider['type'];

        $existing_labels = collect($providers ?? [])->merge($service->defaultConfig('map')['providers'] ?? [])->pluck('label')->reject(function ($value, $key) use ($currentLabel) {
            return $value === $currentLabel;
        })->toArray();

        if($type === 'tile'){
            return [
                'label' => ["sometimes", "required","string", Rule::notIn($existing_labels)],
                'url' => ["sometimes", 'required', 'string', 'regex:/https?:\/\/.+/'],
                'maxZoom' => 'sometimes|integer',
                'subdomains' => 'sometimes|nullable|string',
                'attribution' => 'sometimes|required|nullable|string',
            ];
        }

        return [
            'label' => ["sometimes", "required","string", Rule::notIn($existing_labels)],
            'url' => ["sometimes", 'required', 'string', 'regex:/https?:\/\/.+/'],
            'maxZoom' => 'sometimes|integer',
            'layers' => 'required|string',
            'attribution' => 'sometimes|required|nullable|string',
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
