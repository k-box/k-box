<?php namespace KlinkDMS\Http\Requests;

use KlinkDMS\Http\Requests\Request;
use Illuminate\Contracts\Auth\Guard;

class ProjectRequest extends Request {

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
			'description' => 'sometimes|string',
			'users' => 'required|array|exists:users,id',
			'manager' => 'required|exists:users,id',
			'avatar' => 'sometimes|required|image|max:200'
		];

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
