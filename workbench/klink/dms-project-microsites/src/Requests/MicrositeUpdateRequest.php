<?php namespace Klink\DmsMicrosites\Requests;

use KlinkDMS\Http\Requests\Request;
use Illuminate\Contracts\Auth\Guard;
use KlinkDMS\Capability;
use Klink\DmsMicrosites\Microsite;

class MicrositeUpdateRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
        
        $microsite_id = $this->route('microsites');

		$tests = [
            'title' => 'required|string|not_array',
            'slug' => array('required','string', 'not_array','regex:/^(?!create)[a-z\\-]+/','unique:microsites,slug,' . $microsite_id),
            'description' => 'sometimes|string|not_array',
            'logo' => 'sometimes|string|not_array|url|regex:/^https/',
            'hero_image' => 'sometimes|not_array|string|url|regex:/^https/',
            'default_language' => 'sometimes|not_array|required|string|regex:/^[a-z]{2}$/',
            'content' => 'required|array',
            'menu' => 'sometimes|required|array'
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
        
        if( !$user->can_capability(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH) ){
            return false;
        }
        
        $microsite_id = $this->route('microsites');
        
        try{
            
            $prj = Microsite::findOrFail($microsite_id);
            
            return $prj->user_id === $user->id;
            
        }catch(\Exception $e){
            return false;
        }
        
		return false;
	}

}
