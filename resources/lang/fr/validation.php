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

    
    "accepted"             => "Le champ :attribute doit être accepté.",
    "active_url"           => "Le champ :attribute n'est pas une URL valide.",
    "after"                => "Le champ :attribute doit être une date postérieure au :date.",
    'after_or_equal'       => 'Le champ :attribute doit être une date postérieure ou égale au :date.',
    "alpha"                => "Le champ :attribute doit contenir uniquement des lettres.",
    "alpha_dash"           => "Le champ :attribute doit contenir uniquement des lettres, des chiffres et des tirets.",
    "alpha_num"            => "Le champ :attribute doit contenir uniquement des chiffres et des lettres.",
    "array"                => "Le champ :attribute doit être un tableau.",
    "before"               => "Le champ :attribute doit être une date antérieure au :date.",
    'before_or_equal'      => 'Le champ :attribute doit être une date antérieure ou égale au :date.',
    "between"              => [
        "numeric" => "La valeur de :attribute doit être comprise entre :min et :max.",
        "file"    => "La taille du fichier de :attribute doit être comprise entre :min et :max kilo-octets.",
        "string"  => "Le texte :attribute doit contenir entre :min et :max caractères.",
        "array"   => "Le tableau :attribute doit contenir entre :min et :max éléments.",
    ],
    "boolean"              => "Le champ :attribute doit être vrai ou faux.",
    "confirmed"            => "Le champ de confirmation :attribute ne correspond pas.",
    "date"                 => "Le champ :attribute n'est pas une date valide.",
    "date_format"          => "Le champ :attribute ne correspond pas au format :format.",
    "different"            => "Les champs :attribute et :other doivent être différents.",
    "digits"               => "Le champ :attribute doit contenir :digits chiffres.",
    "digits_between"       => "Le champ :attribute doit contenir entre :min et :max chiffres.",
    "email"                => "Le champ :attribute doit être une adresse courriel valide.",
    "filled"               => "Le champ :attribute doit avoir une valeur.",
    "exists"               => "Le champ :attribute sélectionné est invalide.",
    "image"                => "Le champ :attribute doit être une image.",
    "in"                   => "Le champ :attribute sélectionné est invalide.",
    "integer"              => "Le champ :attribute doit être un entier.",
    "ip"                   => "Le champ :attribute doit être une adresse IP valide.",
    'ipv4'                 => 'Le champ :attribute doit être une adresse IPv4 valide.',
    'ipv6'                 => 'Le champ :attribute doit être une adresse IPv6 valide.',
    "max"                  => [
        "numeric" => "La valeur de :attribute ne peut être supérieure à :max.",
        "file"    => "La taille du fichier de :attribute ne peut pas dépasser :max kilo-octets.",
        "string"  => "Le texte de :attribute ne peut contenir plus de :max caractères.",
        "array"   => "Le tableau :attribute ne peut contenir plus de :max éléments.",
    ],
    "mimes"                => "Le champ :attribute doit être un fichier de type : :values.",
    'mimetypes'            => 'Le champ :attribute doit être un fichier de type : :values..',
    "min"                  => [
        "numeric" => "La valeur de :attribute doit être supérieure ou égale à :min.",
        "file"    => "La taille du fichier de :attribute doit être supérieure à :min kilo-octets.",
        "string"  => "Le texte :attribute doit contenir au moins :min caractères.",
        "array"   => "Le tableau :attribute doit contenir au moins :min éléments.",
    ],
    "not_in"               => "Le champ :attribute sélectionné n'est pas valide",
    "numeric"              => "Le champ :attribute doit contenir un nombre.",
    "regex"                => "Le format du champ :attribute est invalide.",
    "required"             => "Le champ :attribute est obligatoire.",
    "required_if"          => "Le champ :attribute est obligatoire quand la valeur de :other est :value.",
    "required_with"        => "Le champ :attribute est obligatoire quand :values est présent.",
    "required_with_all"    => "Le champ :attribute est obligatoire quand :values est présent.",
    "required_without"     => "Le champ :attribute est obligatoire quand :values n'est pas présent.",
    "required_without_all" => "Le champ :attribute est requis quand aucun de :values n'est présent.",
    "same"                 => "Les champs :attribute et :other doivent être identiques.",
    "size"                 => [
        "numeric" => "La valeur de :attribute doit être :size.",
        "file"    => "La taille du fichier de :attribute doit être de :size kilo-octets.",
        "string"  => "Le texte de :attribute doit contenir :size caractères.",
        "array"   => "Le tableau :attribute doit contenir :size éléments.",
    ],
    'string'               => 'Le champ :attribute doit être une chaîne de caractères.',
    'timezone'             => 'Le champ :attribute doit être un fuseau horaire valide.',
    "unique"               => "La valeur du champ :attribute est déjà utilisée.",
    'uploaded'             => 'Le fichier du champ :attribute n\'a pu être téléversé.',
    "url"                  => 'Le format de l\'URL de :attribute n\'est pas valide.',
    "not_array"             => "Le champ :attribute ne peut pas contenir des valeurs multiples.",

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
            'rule-name' => 'message-personnalisé',
        ],
        'capabilities' => [
            'required' => 'Vous devez spécifier au moins une permission.',
        ],
        'folder_import' => [
            'required_if' => 'Vous devez spécifier un dossier valide pour commencer l\'import.',
        ],
        'remote_import' => [
            'required_if' => 'Vous devez spécifier une URL valide pour commencer l\'import.',
        ],
        'users' => [
            'required' => 'Veuillez sélectionner au moins un utilisateur.',
        ],
        'document' => [
            'required' => 'Le document que vous êtes en train de mettre en ligne dépasse la taille maximale autorisée de '.\Config::get('dms.max_upload_size').' KB',
            'required_alt' => 'Le document que vous êtes en train de mettre en ligne dépasse la taille maximale autorisée de :size :unit',
        ],
        'slug' => [
            'regex' => 'Le nom convivial doit être composé uniquement de lettres minuscules et de tirets. Il ne peut contenir de nombres, ni commencer par "create".',
        ],
        'logo' => [
            'url' => 'Le logo doit être une URL valide vers un fichier image',
            'regex' => 'Le logo doit être une URL valide vers un fichier image',
        ],
        'hero_image' => [
            'url' => 'L\'image plein écran doit être une URL valide vers un fichier image',
            'regex' => 'L\'image plein écran doit être une URL valide vers un fichier image',
        ],
        'with_users' => [
            'required' => 'Vous devez sélectionner au moins un utilisateur',
        ],
        'copyright_owner_website' => [
            'required_without' => 'Vous devez indiquer au moins une méthode de contact. Email et/ou site web sont de bons candidats pour cela.'
        ],
        'copyright_owner_name' => [
            'required' => 'Pour un document public vous devez spécifier le propriétaire des droits d\'auteurs. Cela peut être vous, votre institution ou un tiers.'
        ]        

    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
