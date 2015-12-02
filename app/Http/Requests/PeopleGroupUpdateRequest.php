<?php namespace KlinkDMS\Http\Requests;

use KlinkDMS\Http\Requests\Request;

class PeopleGroupUpdateRequest extends Request {

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
		
		$action = $this->route()->getAction();

		if($action['as'] == 'people.store'){
			return ['name' => 'required'];
		}
		else {
			return [
				'name' => 'sometimes|required',
				'user' => 'sometimes|required|exists:users,id',
				'action' => 'required_with:user|in:add,remove',
				'make_institutional' => 'sometimes|required|boolean',
				'make_personal' => 'sometimes|required|boolean',
			];	
		}

	}

}
