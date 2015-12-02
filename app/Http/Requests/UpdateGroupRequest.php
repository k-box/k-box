<?php namespace KlinkDMS\Http\Requests;

use KlinkDMS\Http\Requests\Request;

class UpdateGroupRequest extends Request {

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
			'name' => 'sometimes|required|regex:/^[\w\d\s\.\-_\(\)]*/',
			'color' => 'sometimes|required|alpha_num|size:6',
			'public' => 'sometimes|required|boolean',
			'private' => 'sometimes|required|boolean',
			'parent' => 'sometimes|required|integer|exists:groups,id',
			'action' => 'required_with:parent|in:move,copy',
			'dry_run' => 'required_with:parent|boolean', // to make a test in case we need to ask the user for continuing the action
		];
	}

}
