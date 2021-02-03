<?php

namespace KBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\Rule;
use KBox\Group;

class CreateShareRequest extends Request
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
            'users' => 'required|exists:users,id',
            'groups' => [
                'required_without:documents',
                Rule::exists('groups', 'id')->where(function ($query) {
                    // limiting the allowed collections to be personal only
                    // https://github.com/k-box/k-box/issues/356
                    $query->whereType(Group::TYPE_PERSONAL);
                }),
            ],
            'documents' => 'required_without:groups|exists:document_descriptors,id'
        ];
    }
}
