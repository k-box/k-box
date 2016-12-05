<?php namespace KlinkDMS\Http\Requests;

use KlinkDMS\Http\Requests\Request;
use Illuminate\Contracts\Auth\Guard;

class AvatarRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{

		// $action = $this->route()->getAction();

		$tests = [
			'avatar' => 'required|image|max:200'
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
		return true; //auth()->check() && auth()->user()->isProjectManager();
	}

}
