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

    
    "accepted"             => " :attribute бояд қабул карда шавад.",
    "active_url"           => " :attribute URL нодуруст аст.",
    "after"                => " :attribute бояд як рузе баъд :date. бошад",
    'after_or_equal'       => " :attribute бояд як рузе баъд ва е дар рузи :date. бошад",
    "alpha"                => " :attribute танҳо аз ҳарф бояд иборат бошад.",
    "alpha_dash"           => " :attribute танҳо аз ҳарф, рақам ва тире бояд иборат бошад.",
    "alpha_num"            => " :attribute танҳо аз ҳарф ва рақам бояд иборат бошад.",
    "array"                => " :attribute бояд рақам бошад.",
    "before"               => " :attribute бояд рузе пеш аз :date. бошад",
    'before_or_equal'      => ' :attribute бояд рузе пеш е баробар ба :date.',
    "between"              => [
        "numeric" => " :attribute бояд байни :min ва :max бошад.",
        "file"    => " :attribute бояд байни :min ва :max килобайт.",
        "string"  => " :attribute бояд байни :min ва :max аломат бошад.",
        "array"   => " :attribute бояд байни :min ва :max чиз бошад.",
    ],
    "boolean"              => " :attribute майдон бояд дуруст ё нодуруст бошад.",
    "confirmed"            => " :attribute ба тасдиқот мувофиқат намекунад.",
    "date"                 => " :attribute санаи руз дуруст нест.",
    "date_format"          => " :attribute ба формат мувофиқат намекунад :format.",
    "different"            => " :attribute ва :other бояд гуногун бошанд.",
    "digits"               => " :attribute бояд :digits ракам бошад.",
    "digits_between"       => " :attribute бояд дар байни :min ва :max бошад.",
    "email"                => " :attribute бояд суроғаи почтаи электронии дуруст бошад.",
    "filled"               => " :attribute майдон бояд кимат дошта бошад.",
    "exists"               => " :attribute интихобшуда нодуруст аст.",
    "image"                => " :attribute бояд акс бошад.",
    "in"                   => " :attribute интихобшуда дуруст нест.",
    "integer"              => " :attribute бояд адади бутун бошад.",
    "ip"                   => " :attribute бояд суроға IP дуруст бошад.",
    'ipv4'                 => ' :attribute бояд суроға IPv4 дуруст бошад.',
    'ipv6'                 => ' :attribute бояд суроға  IPv6 дуруст бошад.',
    "max"                  => [
        "numeric" => " :attribute набояд калонтар аз :max бошад.",
        "file"    => " :attribute калонтар аз :max килобайт набошад.",
        "string"  => " :attribute бояд зиедтар :max аломат набошад.",
        "array"   => " :attribute бояд зиедтар аз :max чиз дошта бошад.",
    ],
    "mimes"                => " :attribute намуди файли бояд чунин бошад: :values.",
    'mimetypes'            => ' :attribute намуди файли бояд чунин бошад: :values.',
    "min"                  => [
        "numeric" => " :attribute бояд ҳадди аққал :min бошад.",
        "file"    => " :attribute бояд ҳадди аққал :min килобайт бошад.",
        "string"  => " :attribute бояд ҳадди аққал :min аломат бошад.",
        "array"   => " :attribute бояд ҳадди аққал :min чиз бошад.",
    ],
    "not_in"               => ":attribute интихобшуда нодуруст аст.",
    "numeric"              => " :attribute бояд адад бошад.",
    "regex"                => " :attribute формат нодуруст аст.",
    "required"             => " :attribute майдон талаб карда мешавад.",
    "required_if"          => " :attribute майдон талаб карда мешавад агар :other ба :value баробар бошад.",
    "required_with"        => " :attribute майдон талаб карда мешавад :values хозира аст.",
    "required_with_all"    => " :attribute майдон талаб карда мешавад :values хозира аст.",
    "required_without"     => " :attribute майдон талаб карда мешавад агар :values хозира набошад.",
    "required_without_all" => " :attribute майдон талаб карда мешавад агар ягон  :values хозира набошад.",
    "same"                 => " :attribute ва :other бояд мувофик бошанд.",
    "size"                 => [
        "numeric" => " :attribute андозаи :size дошта бошад.",
        "file"    => " :attribute бояд :size килобайт бошад.",
        "string"  => " :attribute бояд :size аломат дошта бошад.",
        "array"   => " :attribute бояд дорои :size чиз бошад.",
    ],
    'string'               => ' :attribute бояд сатр бошад.',
    'timezone'             => ' :attribute вақт бояд дуруст интихоб шавад .',
    "unique"               => " :attribute аллакай гирифта шудааст.",
    'uploaded'             => ' :attribute натавонист бор шавад.',
    "url"                  => " :attribute бояд URL дуруст бошад (e.g. http://something.com).",
    "not_array"             => ":attribute бояд якчанд кимат дошта набошад.",

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
            'rule-name' => 'Паём',
        ],
        'capabilities' => [
            'required' => 'Шумо бояд ҳадди ақал як ичозатро муайян кунед.',
        ],
        'users' => [
            'required' => 'Лутфан ҳадди аққал як истифодабарандаро интихоб кунед.',
        ],
        'document' => [
            'required' => 'Санаде, ки шумо бор мекунед, аз андозаи ҳадди аксари иҷозат дода шуда зиед аст '.\KBox\Upload::maximumAsKB().' KB',
            'required_alt' => 'Санаде, ки шумо бор мекунед, аз андозаи ҳадди аксари иҷозат дода шуда зиед аст :size :unit',
        ],
        'slug' => [
            'regex' => 'Cлуг (Slug) бояд бо харфхои хурд ва трие навишат шавад . Бояд ракам ва ситорача надошта бошад е калимаи "create".',
        ],
        'logo' => [
            'url' => 'Лого бояд URL-ро дуруст ба файли тасвир дошта бошад',
            'regex' => 'Лого бояд URL-ро дуруст ба файли тасвир дошта бошад',
        ],
        'hero_image' => [
            'url' => 'Тасвири пурраи экрани бояд URL-и дурустро ба файли тасвир дошта бошад',
            'regex' => 'Тасвири пурраи экрани бояд URL-и дурустро ба файли тасвир дошта бошад',
        ],
        'with_users' => [
            'required' => 'Шумо бояд акалан як истифодабарандаро интихоб кунед'
        ],
        
        'copyright_owner_website' => [
            'required_without' => 'Шумо бояд акалан вебсайт ё почтаро муайян кунед.'
        ],
        'copyright_owner_name' => [
            'required' => 'Шумо бояд соҳиби ҳуқуқи муаллифи ҳуҷҷати нашрро муайян кунед. Он метавонад шумо, ширкати шумо ё шахси сеюм бошад.',
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
