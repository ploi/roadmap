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

    'accepted' => 'L\':attribute deve essere accettato.',
    'accepted_if' => 'L\':attribute deve essere accettato quando :other è :value.',
    'active_url' => 'L\':attribute non è un URL valido.',
    'after' => 'L\':attribute deve essere una data successiva a :date.',
    'after_or_equal' => 'L\':attribute deve essere una data successiva o uguale a :date.',
    'alpha' => 'L\':attribute deve contenere solo lettere.',
    'alpha_dash' => 'L\':attribute deve contenere solo lettere, numeri, trattini e underscore.',
    'alpha_num' => 'L\':attribute deve contenere solo lettere e numeri.',
    'array' => 'L\':attribute deve essere un array.',
    'before' => 'L\':attribute deve essere una data precedente a :date.',
    'before_or_equal' => 'L\':attribute deve essere una data precedente o uguale a :date.',
    'between' => [
        'array' => 'L\':attribute deve avere tra :min e :max elementi.',
        'file' => 'L\':attribute deve essere tra :min e :max kilobyte.',
        'numeric' => 'L\':attribute deve essere tra :min e :max.',
        'string' => 'L\':attribute deve essere tra :min e :max caratteri.',
    ],
    'boolean' => 'Il campo :attribute deve essere vero o falso.',
    'confirmed' => 'La conferma di :attribute non corrisponde.',
    'current_password' => 'La password è errata.',
    'date' => 'L\':attribute non è una data valida.',
    'date_equals' => 'L\':attribute deve essere una data uguale a :date.',
    'date_format' => 'L\':attribute non corrisponde al formato :format.',
    'declined' => 'L\':attribute deve essere rifiutato.',
    'declined_if' => 'L\':attribute deve essere rifiutato quando :other è :value.',
    'different' => 'L\':attribute e :other devono essere diversi.',
    'digits' => 'L\':attribute deve essere di :digits cifre.',
    'digits_between' => 'L\':attribute deve essere tra :min e :max cifre.',
    'dimensions' => 'L\':attribute ha dimensioni dell\'immagine non valide.',
    'distinct' => 'Il campo :attribute ha un valore duplicato.',
    'email' => 'L\':attribute deve essere un indirizzo email valido.',
    'ends_with' => 'L\':attribute deve terminare con uno dei seguenti: :values.',
    'enum' => 'Il :attribute selezionato non è valido.',
    'exists' => 'Il :attribute selezionato non è valido.',
    'file' => 'L\':attribute deve essere un file.',
    'filled' => 'Il campo :attribute deve avere un valore.',
    'gt' => [
        'array' => 'L\':attribute deve avere più di :value elementi.',
        'file' => 'L\':attribute deve essere maggiore di :value kilobyte.',
        'numeric' => 'L\':attribute deve essere maggiore di :value.',
        'string' => 'L\':attribute deve essere maggiore di :value caratteri.',
    ],
    'gte' => [
        'array' => 'L\':attribute deve avere :value elementi o più.',
        'file' => 'L\':attribute deve essere maggiore o uguale a :value kilobyte.',
        'numeric' => 'L\':attribute deve essere maggiore o uguale a :value.',
        'string' => 'L\':attribute deve essere maggiore o uguale a :value caratteri.',
    ],
    'image' => 'L\':attribute deve essere un\'immagine.',
    'in' => 'Il :attribute selezionato non è valido.',
    'in_array' => 'Il campo :attribute non esiste in :other.',
    'integer' => 'L\':attribute deve essere un numero intero.',
    'ip' => 'L\':attribute deve essere un indirizzo IP valido.',
    'ipv4' => 'L\':attribute deve essere un indirizzo IPv4 valido.',
    'ipv6' => 'L\':attribute deve essere un indirizzo IPv6 valido.',
    'json' => 'L\':attribute deve essere una stringa JSON valida.',
    'lt' => [
        'array' => 'L\':attribute deve avere meno di :value elementi.',
        'file' => 'L\':attribute deve essere minore di :value kilobyte.',
        'numeric' => 'L\':attribute deve essere minore di :value.',
        'string' => 'L\':attribute deve essere minore di :value caratteri.',
    ],
    'lte' => [
        'array' => 'L\':attribute non deve avere più di :value elementi.',
        'file' => 'L\':attribute deve essere minore o uguale a :value kilobyte.',
        'numeric' => 'L\':attribute deve essere minore o uguale a :value.',
        'string' => 'L\':attribute deve essere minore o uguale a :value caratteri.',
    ],
    'mac_address' => 'L\':attribute deve essere un indirizzo MAC valido.',
    'max' => [
        'array' => 'L\':attribute non deve avere più di :max elementi.',
        'file' => 'L\':attribute non deve essere maggiore di :max kilobyte.',
        'numeric' => 'L\':attribute non deve essere maggiore di :max.',
        'string' => 'L\':attribute non deve essere maggiore di :max caratteri.',
    ],
    'mimes' => 'L\':attribute deve essere un file di tipo: :values.',
    'mimetypes' => 'L\':attribute deve essere un file di tipo: :values.',
    'min' => [
        'array' => 'L\':attribute deve avere almeno :min elementi.',
        'file' => 'L\':attribute deve essere almeno di :min kilobyte.',
        'numeric' => 'L\':attribute deve essere almeno :min.',
        'string' => 'L\':attribute deve essere almeno di :min caratteri.',
    ],
    'multiple_of' => 'L\':attribute deve essere un multiplo di :value.',
    'not_in' => 'Il :attribute selezionato non è valido.',
    'not_regex' => 'Il formato di :attribute non è valido.',
    'numeric' => 'L\':attribute deve essere un numero.',
    'password' => [
        'mixed' => 'L\':attribute deve contenere almeno una lettera maiuscola e una minuscola.',
        'letters' => 'L\':attribute deve contenere almeno una lettera.',
        'symbols' => 'L\':attribute deve contenere almeno un simbolo.',
        'numbers' => 'L\':attribute deve contenere almeno un numero.',
        'uncompromised' => 'L\':attribute fornito è stato trovato in una violazione di dati. Scegli un altro :attribute.',
    ],
    'present' => 'Il campo :attribute deve essere presente.',
    'prohibited' => 'Il campo :attribute è proibito.',
    'prohibited_if' => 'Il campo :attribute è proibito quando :other è :value.',
    'prohibited_unless' => 'Il campo :attribute è proibito a meno che :other sia in :values.',
    'prohibits' => 'Il campo :attribute proibisce la presenza di :other.',
    'regex' => 'Il formato di :attribute non è valido.',
    'required' => 'Il campo :attribute è richiesto.',
    'required_array_keys' => 'Il campo :attribute deve contenere voci per: :values.',
    'required_if' => 'Il campo :attribute è richiesto quando :other è :value.',
    'required_unless' => 'Il campo :attribute è richiesto a meno che :other sia in :values.',
    'required_with' => 'Il campo :attribute è richiesto quando :values è presente.',
    'required_with_all' => 'Il campo :attribute è richiesto quando sono presenti :values.',
    'required_without' => 'Il campo :attribute è richiesto quando :values non è presente.',
    'required_without_all' => 'Il campo :attribute è richiesto quando nessuno di :values è presente.',
    'same' => 'L\':attribute e :other devono corrispondere.',
    'size' => [
        'array' => 'L\':attribute deve contenere :size elementi.',
        'file' => 'L\':attribute deve essere di :size kilobyte.',
        'numeric' => 'L\':attribute deve essere :size.',
        'string' => 'L\':attribute deve essere di :size caratteri.',
    ],
    'starts_with' => 'L\':attribute deve iniziare con uno dei seguenti: :values.',
    'string' => 'L\':attribute deve essere una stringa.',
    'timezone' => 'L\':attribute deve essere una timezone valida.',
    'unique' => 'L\':attribute è già stato preso.',
    'uploaded' => 'L\':attribute non è riuscito a caricare.',
    'url' => 'L\':attribute deve essere un URL valido.',
    'uuid' => 'L\':attribute deve essere un UUID valido.',

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'messaggio-personalizzato',
        ],
    ],

    'attributes' => [],

];
