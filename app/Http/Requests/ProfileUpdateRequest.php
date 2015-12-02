<?php namespace KlinkDMS\Http\Requests;

use KlinkDMS\Http\Requests\Request;
use KlinkDMS\User;

class ProfileUpdateRequest extends Request {

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
			'name' => 'sometimes|required|alpha_num',
			'email' => 'sometimes|required|email|unique:users,email',
			'password' => 'sometimes|required|alpha_num|min:8',
			'password_confirm' => 'required_with:password|same:password',
			'_change' => 'required|in:pass,mail,info,language',
			User::OPTION_LANGUAGE => 'sometimes|required|in:en,ru'
		];
	}

}
