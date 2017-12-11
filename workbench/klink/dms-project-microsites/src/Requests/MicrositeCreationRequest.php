<?php

namespace Klink\DmsMicrosites\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use KBox\Capability;
use KBox\Project;

class MicrositeCreationRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $tests = [
            'project' => 'bail|required|integer|exists:projects,id',
            'title' => 'bail|required|string|not_array',
            'slug' => ['bail', 'required','string', 'not_array','regex:/^(?!create)[a-z\\-]+/','unique:microsites,slug'],
            'description' => 'bail|sometimes|string|not_array',
            'logo' => ['bail', 'nullable', 'sometimes', 'not_array', 'string', 'url', 'min:5', 'regex:/^https/'],
            'hero_image' => ['bail', 'nullable', 'sometimes', 'not_array', 'string', 'url', 'min:5', 'regex:/^https/'],
            'default_language' => ['bail', 'sometimes', 'required', 'string', 'not_array', 'regex:/^[a-z]{2}$/'],
            'content' => 'bail|required|array',
            'menu' => 'sometimes|required|array'
        ];
        
        /*
            content is a key-value array: key = language code, value MicrositeContent details
            $example = [
                'en' => [
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
        if (! $user->can_capability(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH)) {
            return false;
        }
        
        $project_id = $this->input('project', false);
        
        try {
            $prj = Project::findOrFail($project_id);
            
            return (int)$prj->user_id === (int)$user->id;
        } catch (\Exception $e) {
            return false;
        }
        
        return false;
    }
}
