<?php namespace KlinkDMS\Http\Requests;

use KlinkDMS\Http\Requests\Request;

class SettingsSaveRequest extends Request {

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
			'map_visualization' => 'sometimes|required|in:true',
			'public_core_enabled' => 'sometimes|required|in:true',
			'public_core_url' => 'sometimes|required|url',
			'public_core_username' => 'sometimes|required_with:public_core_url|string',
			'public_core_password' => 'sometimes|required_with:public_core_url|string',
			'public_core_debug' => 'sometimes|required|in:true',
			'support_token' => 'sometimes|string',
		];
	}

}
