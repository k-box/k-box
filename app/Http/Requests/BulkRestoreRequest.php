<?php

namespace KBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class BulkRestoreRequest extends Request
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
            'groups' => 'required_without:documents|exists:groups,id',
            'documents' => 'required_without:groups|exists:document_descriptors,id',
            'context' => 'required|in:trash',
        ];
    }
}
