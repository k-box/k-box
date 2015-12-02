<?php namespace KlinkDMS\Http\Requests;

use KlinkDMS\Http\Requests\Request;
use Illuminate\Contracts\Auth\Guard;

class UserRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{

		$action = $this->route()->getAction();

		$tests = [
			'name' => 'required|string',
			'capabilities' => 'required|array|exists:capabilities,key',
		];

		if($action['as'] == 'administration.users.store'){
			$tests['email'] = 'required|email|unique:users,email';
			$tests['capabilities'] = 'required|array|exists:capabilities,key';
		}
		else {
			$tests['capabilities'] = 'sometimes|required|array|exists:capabilities,key';
			$tests['email'] = 'required|email';
		}

		return $tests;
	}

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

}
