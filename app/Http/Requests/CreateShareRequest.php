<?php namespace KlinkDMS\Http\Requests;

use KlinkDMS\Http\Requests\Request;

class CreateShareRequest extends Request {

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
			// 'name' => 'required|alpha_num',
			'with_people' => 'required_without:with_users|exists:peoplegroup,id',
			'with_users' => 'required_without:with_people|exists:users,id', // alpha_num|size:6
			'groups' => 'required_without:documents|exists:groups,id',
			'documents' => 'required_without:groups|exists:document_descriptors,id'
		];
	}

}
