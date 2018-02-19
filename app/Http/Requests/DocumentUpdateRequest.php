<?php

namespace KBox\Http\Requests;

use KBox\Option;
use KBox\DocumentDescriptor;
use Illuminate\Validation\Rule;
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

        $selectable_licenses = Option::copyright_available_licenses();
        $valid_licenses = Rule::in($selectable_licenses->pluck('id')->toArray());

        $document = DocumentDescriptor::find($this->route('document'));

        $tests = [
            'remove_group' => 'sometimes|required|exists:groups,id',
            'add_group' => 'sometimes|required|exists:groups,id',
            'title' => 'sometimes|required|string|regex:/^[\w\d\s\.\-_\(\)]*/',
            'abstract' => 'nullable|sometimes|string|regex:/^[\w\d\s\.\-_\(\)]*/',
            'language' => 'nullable|sometimes|string|min:2|',
            'authors' => 'nullable|sometimes|string|regex:/^[\w\d\s\.\-_\(\)]*/',
            'copyright_owner_email' => 'nullable|sometimes|string|email',
            'copyright_owner_address' => 'nullable|sometimes|string',
            'copyright_usage' => [
                'nullable',
                'sometimes',
                'string',
                $valid_licenses
            ],
            'copyright_owner_name' => 'nullable|sometimes|string',
            'copyright_owner_website' => 'nullable|sometimes|string|url',

            // if this is present a new file version will be created
            'document' => 'nullable|sometimes|required|between:0,'.$max_size, //new document version
            
        ];

        return $tests;
    }
}
