<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'email' => 'El  :attribute debe ser una dirección de correo válida.',
    'min' => [
        'string' => 'El :attribute debe tener al menos :min caracteres.',
    ],
    'max' => [
        'string' => 'El :attribute no debe superar los :max caracteres.',
    ],
    'confirmed' => 'La confirmación de :attribute no coincide.',
    
    // para los campos de login y registro!
    'custom' => [
        'username' => [
            'not_regex' => 'El :attribute no debe contener espacios ni emojis.',
        ],
        'password' => [
            'not_regex' => 'La :attribute no debe contener espacios ni emojis.',
            'regex' => 'Debe haber al menos 1 mayúscula, 1 minúscula y 1 número.',
        ],
        'email' => [
            'unique' => 'Ya existe una cuenta registrada con ese email.',
        ],
    ],


    'attributes' => [
        'username' => ' "Nombre de usuario" ',
        'email' => ' "Email" ',
        'password' => ' "Contraseña" ',
    ],
];
