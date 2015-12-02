<?php namespace KlinkDMS\Http\Requests;

use KlinkDMS\Http\Requests\Request;

class DocumentUpdateRequest extends Request {

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
		// $action = $this->route()->getAction();

		$tests = [
			'remove_group' => 'sometimes|required|exists:groups,id',
			'add_group' => 'sometimes|required|exists:groups,id',
			'title' => 'sometimes|required|string|regex:/^[\w\d\s\.\-_\(\)]*/',
			'abstract' => 'sometimes|string|regex:/^[\w\d\s\.\-_\(\)]*/',
			'language' => 'sometimes|string|min:2|',
			'authors' => 'sometimes|string|regex:/^[\w\d\s\.\-_\(\)]*/',
			'visibility' => 'sometimes|required|string|in:public,private',
		    
		    // if this is present a new file version will be created and will inherit the 
			'document' => 'sometimes|required|between:0,30000', //new document version
			
		];

		return $tests;
	}

}
