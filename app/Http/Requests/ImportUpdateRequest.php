<?php

namespace KlinkDMS\Http\Requests;

use KlinkDMS\Http\Requests\Request;

class ImportUpdateRequest extends Request
{
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
            
			'retry' => 'required|boolean'
        ];
    }
}
