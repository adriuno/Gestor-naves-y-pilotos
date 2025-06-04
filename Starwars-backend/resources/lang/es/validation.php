<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'email' => 'El :attribute debe ser un email válido.',

    'min' => [
        'string' => 'El :attribute debe tener al menos :min caracteres.',
    ],
    'max' => [
        'string' => 'El :attribute no debe superar los :max caracteres.',
    ],

    'confirmed' => 'La confirmación de :attribute no coincide.',
    'unique' => 'El campo :attribute ya ha sido registrado.',

    'custom' => [
        'username' => [
            'not_regex' => 'El nombre de usuario no debe contener espacios ni emojis.',
            'unique' => 'Nombre de usuario ya en uso.',
        ],
        'password' => [
            'not_regex' => 'La contraseña no puede contener espacios ni emojis.',
            'regex' => 'Debe haber al menos 1 mayúscula, 1 minúscula y 1nº.',
        ],
        'email' => [
            'unique' => 'Email ya registrado.',
        ],
    ],

    'attributes' => [
        'username' => ' "Nombre de usuario" ',
        'email' => ' "Email" ',
        'password' => ' "Contraseña" ',
    ],
];
