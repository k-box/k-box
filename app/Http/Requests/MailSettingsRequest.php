<?php

namespace KBox\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class MailSettingsRequest extends Request
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

        // always needed
        $from = [
            'from_address' => 'required|email',
            'from_name' => 'required|regex:/^[\w\d\s\.\-_@+!\?]*/',
        ];

        // only if driver is different from log
        $server = [];
        if (\Config::get('mail.driver') !== 'log') {
            $server = [
                'pretend' => 'sometimes|required|boolean',
                'host' => 'required|regex:/^[\w\d\.]*/',
                'port' => 'required|integer',
                'encryption' => 'sometimes|required|alpha',
                'smtp_u' => 'sometimes|regex:/^[\w\d\.\-_@+]*/',
                'smtp_p' => 'sometimes|regex:/^[\w\d\.\-_@+!\?]*/',
            ];
        }
        
        return array_merge($from, $server);
    }
}
