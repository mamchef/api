<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines (Lithuanian)
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute laukas turi būti priimtas.',
    'accepted_if' => ':attribute laukas turi būti priimtas, kai :other yra :value.',
    'active_url' => ':attribute laukas turi būti galiojantis URL.',
    'after' => ':attribute laukas turi būti data po :date.',
    'after_or_equal' => ':attribute laukas turi būti data po arba lygi :date.',
    'alpha' => ':attribute laukas gali turėti tik raides.',
    'alpha_dash' => ':attribute laukas gali turėti tik raides, skaičius, brūkšnius ir pabraukimus.',
    'alpha_num' => ':attribute laukas gali turėti tik raides ir skaičius.',
    'any_of' => ':attribute laukas yra neteisingas.',
    'array' => ':attribute laukas turi būti masyvas.',
    'ascii' => ':attribute laukas gali turėti tik vieno baito raide-skaičiuosius simbolius.',
    'before' => ':attribute laukas turi būti data prieš :date.',
    'before_or_equal' => ':attribute laukas turi būti data prieš arba lygi :date.',
    'between' => [
        'array' => ':attribute laukas turi turėti nuo :min iki :max elementų.',
        'file' => ':attribute laukas turi būti nuo :min iki :max kilobaitų.',
        'numeric' => ':attribute laukas turi būti nuo :min iki :max.',
        'string' => ':attribute laukas turi turėti nuo :min iki :max simbolių.',
    ],
    'boolean' => ':attribute laukas turi būti tiesa arba melas.',
    'can' => ':attribute laukas turi neautorizuotą reikšmę.',
    'confirmed' => ':attribute lauko patvirtinimas nesutampa.',
    'contains' => ':attribute laukuje trūksta reikalingos reikšmės.',
    'current_password' => 'Slaptažodis neteisingas.',
    'date' => ':attribute laukas turi būti galiojanti data.',
    'date_equals' => ':attribute laukas turi būti data lygi :date.',
    'date_format' => ':attribute laukas turi atitikti formatą :format.',
    'decimal' => ':attribute laukas turi turėti :decimal dešimtainius skaičius.',
    'declined' => ':attribute laukas turi būti atmestas.',
    'declined_if' => ':attribute laukas turi būti atmestas, kai :other yra :value.',
    'different' => ':attribute laukas ir :other turi skirtis.',
    'digits' => ':attribute laukas turi būti :digits skaitmenų.',
    'digits_between' => ':attribute laukas turi turėti nuo :min iki :max skaitmenų.',
    'dimensions' => ':attribute laukas turi neteisingus vaizdo matmenis.',
    'distinct' => ':attribute laukas turi dublikatų reikšmę.',
    'doesnt_end_with' => ':attribute laukas negali baigtis šiais simboliais: :values.',
    'doesnt_start_with' => ':attribute laukas negali prasidėti šiais simboliais: :values.',
    'email' => ':attribute laukas turi būti galiojantis el. pašto adresas.',
    'ends_with' => ':attribute laukas turi baigtis viena iš šių reikšmių: :values.',
    'enum' => 'Pasirinkta :attribute reikšmė yra neteisinga.',
    'exists' => 'Pasirinkta :attribute reikšmė yra neteisinga.',
    'extensions' => ':attribute laukas turi turėti vieną iš šių plėtinių: :values.',
    'file' => ':attribute laukas turi būti failas.',
    'filled' => ':attribute laukas turi turėti reikšmę.',
    'gt' => [
        'array' => ':attribute laukas turi turėti daugiau nei :value elementų.',
        'file' => ':attribute laukas turi būti didesnis nei :value kilobaitų.',
        'numeric' => ':attribute laukas turi būti didesnis nei :value.',
        'string' => ':attribute laukas turi turėti daugiau nei :value simbolių.',
    ],
    'gte' => [
        'array' => ':attribute laukas turi turėti :value elementų arba daugiau.',
        'file' => ':attribute laukas turi būti didesnis arba lygus :value kilobaitų.',
        'numeric' => ':attribute laukas turi būti didesnis arba lygus :value.',
        'string' => ':attribute laukas turi turėti :value simbolių arba daugiau.',
    ],
    'hex_color' => ':attribute laukas turi būti galiojanti šešioliktainė spalva.',
    'image' => ':attribute laukas turi būti vaizdas.',
    'in' => 'Pasirinkta :attribute reikšmė yra neteisinga.',
    'in_array' => ':attribute laukas neegzistuoja :other.',
    'integer' => ':attribute laukas turi būti sveikasis skaičius.',
    'ip' => ':attribute laukas turi būti galiojantis IP adresas.',
    'ipv4' => ':attribute laukas turi būti galiojantis IPv4 adresas.',
    'ipv6' => ':attribute laukas turi būti galiojantis IPv6 adresas.',
    'json' => ':attribute laukas turi būti galiojanti JSON eilutė.',
    'list' => ':attribute laukas turi būti sąrašas.',
    'lowercase' => ':attribute laukas turi būti mažosiomis raidėmis.',
    'lt' => [
        'array' => ':attribute laukas turi turėti mažiau nei :value elementų.',
        'file' => ':attribute laukas turi būti mažesnis nei :value kilobaitų.',
        'numeric' => ':attribute laukas turi būti mažesnis nei :value.',
        'string' => ':attribute laukas turi turėti mažiau nei :value simbolių.',
    ],
    'lte' => [
        'array' => ':attribute laukas turi turėti ne daugiau nei :value elementų.',
        'file' => ':attribute laukas turi būti mažesnis arba lygus :value kilobaitų.',
        'numeric' => ':attribute laukas turi būti mažesnis arba lygus :value.',
        'string' => ':attribute laukas turi turėti ne daugiau nei :value simbolių.',
    ],
    'mac_address' => ':attribute laukas turi būti galiojantis MAC adresas.',
    'max' => [
        'array' => ':attribute laukas negali turėti daugiau nei :max elementų.',
        'file' => ':attribute laukas negali būti didesnis nei :max kilobaitų.',
        'numeric' => ':attribute laukas negali būti didesnis nei :max.',
        'string' => ':attribute laukas negali turėti daugiau nei :max simbolių.',
    ],
    'max_digits' => ':attribute laukas negali turėti daugiau nei :max skaitmenų.',
    'mimes' => ':attribute laukas turi būti :values tipo failas.',
    'mimetypes' => ':attribute laukas turi būti :values tipo failas.',
    'min' => [
        'array' => ':attribute laukas turi turėti bent :min elementų.',
        'file' => ':attribute laukas turi būti bent :min kilobaitų.',
        'numeric' => ':attribute laukas turi būti bent :min.',
        'string' => ':attribute laukas turi turėti bent :min simbolių.',
    ],
    'min_digits' => ':attribute laukas turi turėti bent :min skaitmenų.',
    'missing' => ':attribute laukas turi būti praleistas.',
    'missing_if' => ':attribute laukas turi būti praleistas, kai :other yra :value.',
    'missing_unless' => ':attribute laukas turi būti praleistas, nebent :other yra :value.',
    'missing_with' => ':attribute laukas turi būti praleistas, kai yra :values.',
    'missing_with_all' => ':attribute laukas turi būti praleistas, kai yra :values.',
    'multiple_of' => ':attribute laukas turi būti :value kartotinis.',
    'not_in' => 'Pasirinkta :attribute reikšmė yra neteisinga.',
    'not_regex' => ':attribute lauko formatas yra neteisingas.',
    'numeric' => ':attribute laukas turi būti skaičius.',
    'password' => [
        'letters' => ':attribute laukas turi turėti bent vieną raidę.',
        'mixed' => ':attribute laukas turi turėti bent vieną didžiąją ir vieną mažąją raidę.',
        'numbers' => ':attribute laukas turi turėti bent vieną skaičių.',
        'symbols' => ':attribute laukas turi turėti bent vieną simbolį.',
        'uncompromised' => 'Nurodytas :attribute buvo pažeistas duomenų saugumo incidente. Pasirinkite kitą :attribute.',
    ],
    'present' => ':attribute laukas turi būti pateiktas.',
    'present_if' => ':attribute laukas turi būti pateiktas, kai :other yra :value.',
    'present_unless' => ':attribute laukas turi būti pateiktas, nebent :other yra :value.',
    'present_with' => ':attribute laukas turi būti pateiktas, kai yra :values.',
    'present_with_all' => ':attribute laukas turi būti pateiktas, kai yra visi :values.',
    'prohibited' => ':attribute laukas yra draudžiamas.',
    'prohibited_if' => ':attribute laukas draudžiamas, kai :other yra :value.',
    'prohibited_unless' => ':attribute laukas draudžiamas, nebent :other yra :values.',
    'prohibits' => ':attribute laukas draudžia :other buvimą.',
    'regex' => ':attribute lauko formatas yra neteisingas.',
    'required' => ':attribute laukas yra privalomas.',
    'required_array_keys' => ':attribute laukas turi turėti įrašus: :values.',
    'required_if' => ':attribute laukas yra privalomas, kai :other yra :value.',
    'required_if_accepted' => ':attribute laukas yra privalomas, kai priimtas :other.',
    'required_if_declined' => ':attribute laukas yra privalomas, kai atmestas :other.',
    'required_unless' => ':attribute laukas yra privalomas, nebent :other yra :values.',
    'required_with' => ':attribute laukas yra privalomas, kai yra :values.',
    'required_with_all' => ':attribute laukas yra privalomas, kai yra visi :values.',
    'required_without' => ':attribute laukas yra privalomas, kai nėra :values.',
    'required_without_all' => ':attribute laukas yra privalomas, kai nėra nė vieno iš :values.',
    'same' => ':attribute laukas ir :other turi sutapti.',
    'size' => [
        'array' => ':attribute laukas turi turėti :size elementų.',
        'file' => ':attribute laukas turi būti :size kilobaitų.',
        'numeric' => ':attribute laukas turi būti :size.',
        'string' => ':attribute laukas turi turėti :size simbolių.',
    ],
    'starts_with' => ':attribute laukas turi prasidėti viena iš šių reikšmių: :values.',
    'string' => ':attribute laukas turi būti eilutė.',
    'timezone' => ':attribute laukas turi būti galiojanti laiko zona.',
    'unique' => ':attribute jau egzistuoja.',
    'uploaded' => ':attribute nepavyko įkelti.',
    'uppercase' => ':attribute laukas turi būti didžiosiomis raidėmis.',
    'url' => ':attribute laukas turi būti galiojantis URL.',
    'ulid' => ':attribute laukas turi būti galiojantis ULID.',
    'uuid' => ':attribute laukas turi būti galiojantis UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
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

    'attributes' => [
        'name' => 'vardas',
        'username' => 'vartotojo vardas',
        'email' => 'el. paštas',
        'phone' => 'telefono numeris',
        'phone_number' => 'telefono numeris',
        'first_name' => 'vardas',
        'last_name' => 'pavardė',
        'password' => 'slaptažodis',
        'password_confirmation' => 'slaptažodžio patvirtinimas',
        'country_code' => 'šalies kodas',
        'city' => 'miestas',
        'city_id' => 'miestas',
        'address' => 'adresas',
        'zip' => 'pašto kodas',
        'state' => 'valstija',
        'country' => 'šalis',
        'date' => 'data',
        'time' => 'laikas',
        'available' => 'prieinama',
        'size' => 'dydis',
        'file' => 'failas',
        'image' => 'vaizdas',
        'price' => 'kaina',
        'quantity' => 'kiekis',
        'title' => 'pavadinimas',
        'description' => 'aprašymas',
        'content' => 'turinys',
        'category' => 'kategorija',
        'tag' => 'žyma',
        'status' => 'būsena',
        'type' => 'tipas',
        'code' => 'kodas',
        'fcm_token' => 'FCM žeton',
        'id_number' => 'asmens kodas',
        'main_street' => 'gatvė',
        'chef_store_id' => 'virtuvė',
        'delivery_type' => 'pristatymo tipas',
        'user_address' => 'pristatymo adresas',
        'user_notes' => 'pastabos',
        'payment_method' => 'mokėjimo būdas',
        'items' => 'prekės',
        'food_id' => 'maistas',
        'note' => 'pastaba',
        'options' => 'parinktys',
        'food_option_group_id' => 'parinkčių grupė',
        'food_option_id' => 'parinktis',
        'user_address_id' => 'pristatymo adresas',
        'different_password' => 'dabartinis slaptažodis ir naujas slaptažodis negali būti vienodi!',
        'current_password_not_match' => 'dabartinis slaptažodis nesutampa!',
    ],
];