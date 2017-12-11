<?php

namespace KBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class ImportRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        // $action = $this->route()->getAction();

        $tests = [
            'from' => 'required|in:folder,remote',
            'folder_import' => 'required_if:from,folder', //TODO: check for alpha chars plus backslash
            'remote_import' => 'required_if:from,remote',
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
        return true; // would be great to handle user check here :)
    }
}
