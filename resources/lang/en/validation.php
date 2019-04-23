<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    
    "accepted" => "The :attribute must be accepted.",
    "active_url" => "The :attribute is not a valid URL.",
    "after" => "The :attribute must be a date after :date.",
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    "alpha" => "The :attribute may only contain letters.",
    "alpha_dash" => "The :attribute may only contain letters, numbers, dashes and underscores.",
    "alpha_num" => "The :attribute may only contain letters and numbers.",
    "array" => "The :attribute must be an array.",
    "before" => "The :attribute must be a date before :date.",
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    "between" => [
        "numeric" => "The :attribute must be between :min and :max.",
        "file" => "The :attribute must be between :min and :max kilobytes.",
        "string" => "The :attribute must be between :min and :max characters.",
        "array" => "The :attribute must have between :min and :max items.",
    ],
    "boolean" => "The :attribute field must be true or false.",
    "confirmed" => "The :attribute confirmation does not match.",
    "date" => "The :attribute is not a valid date.",
    "date_format" => "The :attribute does not match the format :format.",
    "different" => "The :attribute and :other must be different.",
    "digits" => "The :attribute must be :digits digits.",
    "digits_between" => "The :attribute must be between :min and :max digits.",
    "email" => "The :attribute must be a valid email address.",
    "filled" => "The :attribute field is must have a value.",
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    "exists" => "The selected :attribute is invalid.",
    "image" => "The :attribute must be an image.",
    "in" => "The selected :attribute is invalid.",
    'in_array' => 'The :attribute field does not exist in :other.',
    "integer" => "The :attribute must be an integer.",
    "ip" => "The :attribute must be a valid IP address.",
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    "max" => [
        "numeric" => "The :attribute may not be greater than :max.",
        "file" => "The :attribute may not be greater than :max kilobytes.",
        "string" => "The :attribute may not be greater than :max characters.",
        "array" => "The :attribute may not have more than :max items.",
    ],
    "mimes" => "The :attribute must be a file of type: :values.",
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    "min" => [
        "numeric" => "The :attribute must be at least :min.",
        "file" => "The :attribute must be at least :min kilobytes.",
        "string" => "The :attribute must be at least :min characters.",
        "array" => "The :attribute must have at least :min items.",
    ],
    "not_in" => "The selected :attribute is invalid.",
    'not_regex' => 'The :attribute format is invalid.',
    "numeric" => "The :attribute must be a number.",
    'present' => 'The :attribute field must be present.',
    "regex" => "The :attribute format is invalid.",
    "required" => "The :attribute field is required.",
    "required_if" => "The :attribute field is required when :other is :value.",
    "required_with" => "The :attribute field is required when :values is present.",
    "required_with_all" => "The :attribute field is required when :values are present.",
    "required_without" => "The :attribute field is required when :values is not present.",
    "required_without_all" => "The :attribute field is required when none of :values are present.",
    "same" => "The :attribute and :other must match.",
    "size" => [
        "numeric" => "The :attribute must be :size.",
        "file" => "The :attribute must be :size kilobytes.",
        "string" => "The :attribute must be :size characters.",
        "array" => "The :attribute must contain :size items.",
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute must be a valid URL (e.g. http://something.com).',
    'uuid' => 'The :attribute must be a valid UUID.',
    "not_array" => "The :attribute must not contain multiple values.",

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'capabilities' => [
            'required' => 'You must specify at least one permission.',
        ],
        'users' => [
            'required' => 'Please select at least one user.',
        ],
        'document' => [
            'required' => 'The document you are uploading exceeds the maximum allowed size of '.\Config::get('dms.max_upload_size').' KB',
            'required_alt' => 'The document you are uploading exceeds the maximum allowed size of :size :unit',
        ],
        'slug' => [
            'regex' => 'The slug must be made of lower case characters with dashes. Must not contain numbers or start with "create".',
        ],
        'logo' => [
            'url' => 'The logo must be a valid URL to an image file',
            'regex' => 'The logo must be a valid URL to an image file',
        ],
        'hero_image' => [
            'url' => 'The full screen image must be a valid URL to an image file',
            'regex' => 'The full screen image must be a valid URL to an image file',
        ],
        'with_users' => [
            'required' => 'You need to select at least one user',
        ],
        'copyright_owner_website' => [
            'required_without' => 'You should at least specify a contact method. The website and/or an email might be a good candidate.'
        ],
        'copyright_owner_name' => [
            'required' => 'For a public document you have to specify who owns the copyright. It can be you, your company or a third party.'
        ],
        'send_password' => [
            'accepted' => 'If you don\'t send the autogenerated password, the user will not know what password to use'
        ]


    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    | More examples:
    |  'copyright_owner_website' => 'Copyright Owner Website', // when editing file
    |  'copyright_owner_email' => 'Copyright Owner E-Mail', // when editing file
    |  'organization_website' => 'Organization Website', // when editing your profile
    |  'password' => 'Password', // when changing own password, or creating user 
    |  'password_confirm' => 'Password Confirm', // when changing own email
    |  'email' => 'E-Mail',
    |  'avatar' => 'Project Avatar', // when creating project
    |  'name' => 'Name', // user name or project name
    |  'available_licenses' => 'Available Licenses', // when setting default liceses
    |  'geoserver_url' => 'Geoserver URL', // in geoplugin settings
    |  'website' => 'Institution Website Address', // in Identity settings
    |  'image' => 'Institution image or avatar (url of an image)', // in Identity settings
    |
    */

    'attributes' => [],


];
