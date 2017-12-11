<?php

namespace KBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class DocumentUpdateRequest extends Request
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
        // $action = $this->route()->getAction();
        
        $max_size = \Config::get('dms.max_upload_size');

        $tests = [
            'remove_group' => 'sometimes|required|exists:groups,id',
            'add_group' => 'sometimes|required|exists:groups,id',
            'title' => 'sometimes|required|string|regex:/^[\w\d\s\.\-_\(\)]*/',
            'abstract' => 'nullable|sometimes|string|regex:/^[\w\d\s\.\-_\(\)]*/',
            'language' => 'nullable|sometimes|string|min:2|',
            'authors' => 'nullable|sometimes|string|regex:/^[\w\d\s\.\-_\(\)]*/',
            'visibility' => 'nullable|sometimes|required|string|in:public,private',
            
            // if this is present a new file version will be created and will inherit the
            'document' => 'nullable|sometimes|required|between:0,'.$max_size, //new document version
            
        ];

        return $tests;
    }
}
