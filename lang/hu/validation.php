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

    'accepted' => 'A(z) :attribute mezőt el kell fogadnod.',
    'accepted_if' => 'A(z) :attribute mezőt el kell fogadnod, ha a(z) :other értéke :value.',
    'active_url' => 'A(z) :attribute nem érvényes URL.',
    'after' => 'A(z) :attribute dátumnak :date utáni időpontnak kell lennie.',
    'after_or_equal' => 'A(z) :attribute dátumnak :date utáni vagy azzal egyenlő időpontnak kell lennie.',
    'alpha' => 'A(z) :attribute csak betűket tartalmazhat.',
    'alpha_dash' => 'A(z) :attribute csak betűket, számokat, kötőjeleket és aláhúzásokat tartalmazhat.',
    'alpha_num' => 'A(z) :attribute csak betűket és számokat tartalmazhat.',
    'array' => 'A(z) :attribute mező tömb kell legyen.',
    'before' => 'A(z) :attribute dátumnak :date előtti időpontnak kell lennie.',
    'before_or_equal' => 'A(z) :attribute dátumnak :date előtti vagy azzal egyenlő időpontnak kell lennie.',
    'between' => [
        'array' => 'A(z) :attribute :min és :max közötti elemet kell tartalmazzon.',
        'file' => 'A(z) :attribute mérete :min és :max kilobájt között kell legyen.',
        'numeric' => 'A(z) :attribute értékének :min és :max között kell lennie.',
        'string' => 'A(z) :attribute :min és :max karakter között kell legyen.',
    ],
    'boolean' => 'A(z) :attribute mező csak igaz vagy hamis lehet.',
    'confirmed' => 'A(z) :attribute megerősítés nem egyezik.',
    'current_password' => 'A jelszó helytelen.',
    'date' => 'A(z) :attribute nem érvényes dátum.',
    'date_equals' => 'A(z) :attribute dátumnak :date-dal egyenlőnek kell lennie.',
    'date_format' => 'A(z) :attribute nem felel meg a következő formátumnak: :format.',
    'declined' => 'A(z) :attribute értéket el kell utasítanod.',
    'declined_if' => 'A(z) :attribute értéket el kell utasítanod, ha :other értéke :value.',
    'different' => 'A(z) :attribute és :other különböző kell legyen.',
    'digits' => 'A(z) :attribute :digits számjegyből kell álljon.',
    'digits_between' => 'A(z) :attribute :min és :max számjegy között kell legyen.',
    'dimensions' => 'A(z) :attribute érvénytelen kép méretekkel rendelkezik.',
    'distinct' => 'A(z) :attribute mező értéke duplikált.',
    'email' => 'A(z) :attribute érvényes e-mail cím kell legyen.',
    'ends_with' => 'A(z) :attribute a következők egyikével kell végződjön: :values.',
    'enum' => 'A kiválasztott :attribute érvénytelen.',
    'exists' => 'A kiválasztott :attribute érvénytelen.',
    'file' => 'A(z) :attribute fájl kell legyen.',
    'filled' => 'A(z) :attribute mező nem lehet üres.',
    'gt' => [
        'array' => 'A(z) :attribute több mint :value elemet kell tartalmazzon.',
        'file' => 'A(z) :attribute nagyobb kell legyen, mint :value kilobájt.',
        'numeric' => 'A(z) :attribute nagyobb kell legyen, mint :value.',
        'string' => 'A(z) :attribute több karakter kell legyen, mint :value.',
    ],
    'gte' => [
        'array' => 'A(z) :attribute legalább :value elemet kell tartalmazzon.',
        'file' => 'A(z) :attribute legalább :value kilobájt kell legyen.',
        'numeric' => 'A(z) :attribute nagyobb vagy egyenlő kell legyen, mint :value.',
        'string' => 'A(z) :attribute legalább :value karakter kell legyen.',
    ],
    'image' => 'A(z) :attribute kép kell legyen.',
    'in' => 'A kiválasztott :attribute érvénytelen.',
    'in_array' => 'A(z) :attribute mező nem létezik a(z) :other mezőben.',
    'integer' => 'A(z) :attribute egész szám kell legyen.',
    'ip' => 'A(z) :attribute érvényes IP cím kell legyen.',
    'ipv4' => 'A(z) :attribute érvényes IPv4 cím kell legyen.',
    'ipv6' => 'A(z) :attribute érvényes IPv6 cím kell legyen.',
    'json' => 'A(z) :attribute érvényes JSON szöveg kell legyen.',
    'lt' => [
        'array' => 'A(z) :attribute kevesebb, mint :value elemet kell tartalmazzon.',
        'file' => 'A(z) :attribute kisebb kell legyen, mint :value kilobájt.',
        'numeric' => 'A(z) :attribute kisebb kell legyen, mint :value.',
        'string' => 'A(z) :attribute kevesebb, mint :value karakter kell legyen.',
    ],
    'lte' => [
        'array' => 'A(z) :attribute nem tartalmazhat több, mint :value elemet.',
        'file' => 'A(z) :attribute legfeljebb :value kilobájt lehet.',
        'numeric' => 'A(z) :attribute legfeljebb :value lehet.',
        'string' => 'A(z) :attribute legfeljebb :value karakter lehet.',
    ],
    'mac_address' => 'A(z) :attribute érvényes MAC cím kell legyen.',
    'max' => [
        'array' => 'A(z) :attribute nem tartalmazhat több, mint :max elemet.',
        'file' => 'A(z) :attribute nem lehet nagyobb, mint :max kilobájt.',
        'numeric' => 'A(z) :attribute nem lehet nagyobb, mint :max.',
        'string' => 'A(z) :attribute nem lehet több, mint :max karakter.',
    ],
    'mimes' => 'A(z) :attribute fájltípus a következő kell legyen: :values.',
    'mimetypes' => 'A(z) :attribute fájltípus a következő kell legyen: :values.',
    'min' => [
        'array' => 'A(z) :attribute legalább :min elemet kell tartalmazzon.',
        'file' => 'A(z) :attribute legalább :min kilobájt kell legyen.',
        'numeric' => 'A(z) :attribute legalább :min kell legyen.',
        'string' => 'A(z) :attribute legalább :min karakter kell legyen.',
    ],
    'multiple_of' => 'A(z) :attribute :value többszöröse kell legyen.',
    'not_in' => 'A kiválasztott :attribute érvénytelen.',
    'not_regex' => 'A(z) :attribute formátuma érvénytelen.',
    'numeric' => 'A(z) :attribute szám kell legyen.',
    'password' => [
        'mixed' => 'A(z) :attribute legalább egy nagybetűt és egy kisbetűt kell tartalmazzon.',
        'letters' => 'A(z) :attribute legalább egy betűt kell tartalmazzon.',
        'symbols' => 'A(z) :attribute legalább egy szimbólumot kell tartalmazzon.',
        'numbers' => 'A(z) :attribute legalább egy számot kell tartalmazzon.',
        'uncompromised' => 'A(z) :attribute szerepel egy adatvédelmi incidensben. Kérlek, válassz másik jelszót.',
    ],
    'present' => 'A(z) :attribute mezőnek jelen kell lennie.',
    'prohibited' => 'A(z) :attribute mező tiltott.',
    'prohibited_if' => 'A(z) :attribute mező tiltott, ha :other értéke :value.',
    'prohibited_unless' => 'A(z) :attribute mező tiltott, kivéve ha :other benne van a következőkben: :values.',
    'prohibits' => 'A(z) :attribute mező tiltja, hogy :other jelen legyen.',
    'regex' => 'A(z) :attribute formátuma érvénytelen.',
    'required' => 'A(z) :attribute mező kötelező.',
    'required_array_keys' => 'A(z) :attribute mezőnek tartalmaznia kell a következő bejegyzéseket: :values.',
    'required_if' => 'A(z) :attribute mező kötelező, ha :other értéke :value.',
    'required_unless' => 'A(z) :attribute mező kötelező, kivéve ha :other benne van a következőkben: :values.',
    'required_with' => 'A(z) :attribute mező kötelező, ha :values jelen van.',
    'required_with_all' => 'A(z) :attribute mező kötelező, ha :values jelen vannak.',
    'required_without' => 'A(z) :attribute mező kötelező, ha :values nincs jelen.',
    'required_without_all' => 'A(z) :attribute mező kötelező, ha egyik sem jelenik meg a következőkből: :values.',
    'same' => 'A(z) :attribute és :other egyeznie kell.',
    'size' => [
        'array' => 'A(z) :attribute pontosan :size elemet kell tartalmazzon.',
        'file' => 'A(z) :attribute :size kilobájt kell legyen.',
        'numeric' => 'A(z) :attribute :size kell legyen.',
        'string' => 'A(z) :attribute :size karakter kell legyen.',
    ],
    'starts_with' => 'A(z) :attribute a következők egyikével kell kezdődjön: :values.',
    'string' => 'A(z) :attribute szöveg kell legyen.',
    'timezone' => 'A(z) :attribute érvényes időzóna kell legyen.',
    'unique' => 'A(z) :attribute már foglalt.',
    'uploaded' => 'A(z) :attribute feltöltése sikertelen.',
    'url' => 'A(z) :attribute érvényes URL kell legyen.',
    'uuid' => 'A(z) :attribute érvényes UUID kell legyen.',

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
            'rule-name' => 'egyedi-üzenet',
        ],
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
    */

    'attributes' => [],

];
