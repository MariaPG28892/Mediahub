<?php

return [

    'required' => 'El campo :attribute es obligatorio.',
    'email' => 'El campo :attribute debe ser un correo válido.',
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'confirmed' => 'Las contraseñas no coinciden.',
    'unique' => 'El :attribute ya está en uso.',
    'date' => 'El campo :attribute debe ser una fecha válida.',
    'before' => 'El campo :attribute debe ser anterior a hoy.',

    'attributes' => [
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
        'name' => 'nombre',
        'nombre_usuario' => 'nombre de usuario',
        'fecha_nacimiento' => 'fecha de nacimiento',
    ],

];