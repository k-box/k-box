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


    "accepted"             => ":attribute muss akzeptiert sein.",
    "active_url"           => ":attribute ist keine gültige URL.",
    "after"                => ":attribute muss ein Datum nach dem :date sein.",
    'after_or_equal'       => ':attribute muss ein Datum nach oder gleich dem :date sein.',
    "alpha"                => ":attribute darf nur Buchstaben enthalten.",
    "alpha_dash"           => ":attribute darf nur Buchstaben, Zahlen und Bindestriche enthalten.",
    "alpha_num"            => ":attribute darf nur Buchstaben und Zahlen enthalten.",
    "array"                => ":attribute muss ein Array sein.",
    "before"               => ":attribute muss ein Datum vor dem :date sein.",
    'before_or_equal'      => ':attribute muss ein Datum vor oder gleich dem :date sein.',
    "between"              => [
        "numeric" => ":attribute muss zwischen :min und :max liegen.",
        "file"    => ":attribute muss zwischen :min und :max Kilobyte groß sein.",
        "string"  => ":attribute muss zwischen :min und :max Zeichen haben.",
        "array"   => ":attribute muss zwischen :min und :max Elemente haben.",
    ],
    "boolean"              => ":attribute muss wahr oder falsch sein.",
    "confirmed"            => ":attribute wurde nicht bestätigt.",
    "date"                 => ":attribute ist kein valides Datum.",
    "date_format"          => ":attribute ist nicht im Format :format.",
    "different"            => ":attribute und :other müssen sich unterscheiden.",
    "digits"               => ":attribute muss :digits Ziffern haben.",
    "digits_between"       => ":attribute muss zwischen :min und :max Ziffen haben.",
    "email"                => ":attribute muss eine E-Mailadresse sein.",
    "filled"               => ":attribute muss einen Wert haben.",
    "exists"               => ":attribute ist ungültig.",
    "image"                => ":attribute muss ein Bild sein.",
    "in"                   => ":attribute hat einen ungültigen Wert.",
    "integer"              => ":attribute muss eine Ganzzahl sein.",
    "ip"                   => ":attribute muss eine IP Adresse sein.",
    'ipv4'                 => ':attribute muss eine IPv4 Adresse sein.',
    'ipv6'                 => ':attribute muss eine IPv6 Adresse sein.',
    "max"                  => [
        "numeric" => ":attribute darf nicht größer sein als :max.",
        "file"    => ":attribute darf nicht größer sein als :max Kilobyte.",
        "string"  => ":attribute darf nicht länger sein als :max Zeichen.",
        "array"   => ":attribute darf nicht mehr als :max Elemente haben.",
    ],
    "mimes"                => ":attribute muss eine Datei vom Typ: :values sein.",
    'mimetypes'            => ':attribute muss eine Datei vom Typ: :values sein.',
    "min"                  => [
        "numeric" => ":attribute muss mindestens :min sein.",
        "file"    => ":attribute muss mindestens :min Kilobyte groß sein.",
        "string"  => ":attribute muss mindestens :min Zeichen haben.",
        "array"   => ":attribute muss mindestens :min Elemente haben.",
    ],
    "not_in"               => ":attribute ist ungültig.",
    "numeric"              => ":attribute muss eine Nummer sein.",
    "regex"                => ":attribute ist im falschen Format.",
    "required"             => ":attribute wird benötigt.",
    "required_if"          => ":attribute wird benötigt, falls :other :value ist.",
    "required_with"        => ":attribute wird benötigt wenn :values angegeben wurde.",
    "required_with_all"    => ":attribute wird benötigt wenn :values angegeben wurde.",
    "required_without"     => ":attribute wird benötigt wenn :values nicht angegeben wurde.",
    "required_without_all" => ":attribute wird benötigt wenn keines von :values angegben wurde.",
    "same"                 => ":attribute und :other müssen gleich sein.",
    "size"                 => [
        "numeric" => ":attribute muss :size sein.",
        "file"    => ":attribute muss :size Kilobyte sein.",
        "string"  => ":attribute muss :size Zeichen enthalten.",
        "array"   => ":attribute must muss genau :size Elemente haben.",
    ],
    'string'               => ':attribute muss eine Zeichenkette sein.',
    'timezone'             => ':attribute muss eine gültige Zeitzone sein.',
    "unique"               => ":attribute ist bereits vergeben.",
    'uploaded'             => ':attribute konnte nicht hochgeladen werden.',
    "url"                  => ":attribute muss eine gültige URL sein (z.B. http://example.com).",
    "not_array"            => ":attribute darf nicht mehrere Werte enthalten.",

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
            'required' => 'Sie müssen mindestens eine Berechtigung angeben.',
        ],
        'users' => [
            'required' => 'Bitte zumindest einen Nutzer auswählen.',
        ],
        'document' => [
            'required' => 'Das Dokument das Sie hochladen wollen ist größer als die erlaubten '.\Config::get('dms.max_upload_size').' KB',
            'required_alt' => 'Das Dokument das Sie hochladen wollen ist größer als die erlaubten :size :unit',
        ],
        'slug' => [
            'regex' => 'Der Slug darf nur Kleinbuchstaben und Bindestriche enthalten. Er darf keine Zahlen enthalten, oder mit "create" anfangen.',
        ],
        'logo' => [
            'url' => 'Logo muss ein gültiger Link zu einer Bilddatei sein',
            'regex' => 'Logo muss ein gültiger Link zu einer Bilddatei sein',
        ],
        'hero_image' => [
            'url' => 'Das Titelbild muss ein gültiger Link zu einer Bilddatei sein',
            'regex' => 'Das Titelbild muss ein gültiger Link zu einer Bilddatei sein',
        ],
        'with_users' => [
            'required' => 'Es muss mindestens ein Nutzer ausgewählt werden',
        ],
        'copyright_owner_website' => [
            'required_without' => 'Sie sollten zumindest Kontaktinformationen angeben. Webseite und/oder E-Mailadresse bieten sich an.'
        ],
        'copyright_owner_name' => [
            'required' => 'Für ein veröffentlichtes Dokument müssen sie einen Rechteinhaber angeben. Das könnten Sie sein, ihre Firma oder eine fremde Person.'
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
