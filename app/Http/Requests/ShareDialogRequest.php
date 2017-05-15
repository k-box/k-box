<?php

namespace KlinkDMS\Http\Requests;

use KlinkDMS\Http\Requests\Request;
use KlinkDMS\Capability;

class ShareDialogRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = auth()->user();
        return $user->can_capability(Capability::SHARE_WITH_PRIVATE) || $user->can_capability(Capability::SHARE_WITH_PERSONAL);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'collections' => 'exists:groups,id',
			'documents' => 'exists:document_descriptors,id'
        ];
    }
}
