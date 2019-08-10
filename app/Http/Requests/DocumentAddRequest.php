<?php

namespace KBox\Http\Requests;

use KBox\Upload;
use Illuminate\Foundation\Http\FormRequest as Request;

class DocumentAddRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $max_size = Upload::maximum();

        $tests = [
            'group' => 'sometimes|required|exists:groups,id',
            'document' => 'required|file|between:0,'.$max_size,
            'document_fullpath' => 'sometimes|required',
            'document_name' => 'sometimes|required',
            'folder_path' => 'sometimes|required|min:1',
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

    public function messages()
    {
        return [
            'document.uploaded' => trans('errors.upload.file_not_uploaded', ['max_size' => Upload::maximumAsKB().' KB']),
        ];
    }
}
