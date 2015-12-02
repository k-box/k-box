<?php namespace KlinkDMS\Http\Requests;

use KlinkDMS\Http\Requests\Request;
use KlinkDMS\User;

class UserOptionUpdateRequest extends Request {

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
			User::OPTION_LIST_TYPE => 'sometimes|required|in:details,tiles,cards',
			User::OPTION_LANGUAGE => 'sometimes|required|in:en,ru'
		];
	}

}
