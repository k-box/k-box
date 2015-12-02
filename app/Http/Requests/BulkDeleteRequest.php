<?php namespace KlinkDMS\Http\Requests;

use KlinkDMS\Http\Requests\Request;

class BulkDeleteRequest extends Request {

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
			'groups' => 'required_without_all:documents|exists:groups,id',
			'documents' => 'required_without_all:groups|exists:document_descriptors,id',
			'context' => 'sometimes|required|in:public,private,all,group,starred,trash,shared,recent',
			'force' => 'sometimes|required|boolean'
		];
	}

}
