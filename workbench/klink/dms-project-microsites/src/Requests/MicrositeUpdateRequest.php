<?php

namespace Klink\DmsMicrosites\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Rule;
use KBox\Capability;
use Klink\DmsMicrosites\Microsite;

class MicrositeUpdateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $microsite_id = $this->route('microsite');

        $tests = [
            'title' => 'bail|required|string|not_array',
            'slug' => ['bail', 'required','string', 'not_array', 'alpha_dash', 'min:3',
                Rule::unique('microsites', 'slug')->ignore($microsite_id),
            ],
            'description' => 'bail|sometimes|string|not_array',
            'logo' => 'bail|nullable|sometimes|string|not_array|url|regex:/^https/',
            'hero_image' => 'bail|nullable|sometimes|not_array|string|url|regex:/^https/',
            'default_language' => 'bail|sometimes|not_array|required|string|regex:/^[a-z]{2}$/',
            'content' => 'bail|required|array',
            'menu' => 'bail|sometimes|required|array'
        ];
        
        /*
            content is a key-value array: key = language code, value MicrositeContent details
            $example = [
                'en' => [
                    'id' => THE_PAGE_ID,
                    'title' => 'Example page',
                    'slug' => 'Example page',
                    'content' => 'Example page content',
                ]
            ];
        */
        
        

        return $tests;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // user must be a project admin
        // user must be the same as the project owner
      
        $user = $this->user();
        
        if (! $user->can_capability(Capability::$PROJECT_MANAGER_LIMITED)) {
            return false;
        }
        
        $microsite_id = $this->route('microsite');
        
        try {
            $prj = Microsite::findOrFail($microsite_id);
            
            return $prj->user_id === $user->id;
        } catch (\Exception $e) {
            return false;
        }
        
        return false;
    }
}
