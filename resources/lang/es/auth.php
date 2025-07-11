<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
    'password' => 'La contraseña proporcionada es incorrecta.',
    'throttle' => 'Demasiados intentos de inicio de sesión. Por favor intente nuevamente en :seconds segundos.',

    // Additional authentication messages
    'login' => [
        'title' => 'Iniciar Sesión',
        'subtitle' => 'Accede a tu panel de administración',
        'email' => 'Correo Electrónico',
        'password' => 'Contraseña',
        'remember' => 'Recordarme en este dispositivo',
        'submit' => 'Iniciar Sesión',
        'forgot_password' => '¿Olvidaste tu contraseña?',
        'welcome_back' => '¡Bienvenido de vuelta!',
        'please_sign_in' => 'Por favor inicia sesión en tu cuenta',
    ],

    'logout' => [
        'success' => 'Has cerrado sesión exitosamente.',
        'confirm' => '¿Estás seguro de que deseas cerrar sesión?',
    ],

    'register' => [
        'title' => 'Crear Cuenta',
        'subtitle' => 'Regístrate para acceder al sistema',
        'name' => 'Nombre Completo',
        'email' => 'Correo Electrónico',
        'password' => 'Contraseña',
        'password_confirmation' => 'Confirmar Contraseña',
        'submit' => 'Crear Cuenta',
        'already_have_account' => '¿Ya tienes una cuenta?',
        'sign_in_here' => 'Inicia sesión aquí',
    ],

    'reset' => [
        'title' => 'Recuperar Contraseña',
        'subtitle' => 'Te enviaremos un enlace para restablecer tu contraseña',
        'email' => 'Correo Electrónico',
        'submit' => 'Enviar Enlace de Recuperación',
        'back_to_login' => 'Volver al inicio de sesión',
        'instructions' => 'Ingresa tu correo electrónico y te enviaremos un enlace seguro para crear una nueva contraseña.',
    ],

    'passwords' => [
        'reset_title' => 'Restablecer Contraseña',
        'reset_subtitle' => 'Crea una contraseña segura para tu cuenta',
        'new_password' => 'Nueva Contraseña',
        'confirm_password' => 'Confirmar Contraseña',
        'submit' => 'Restablecer Contraseña',
        'requirements' => [
            'title' => 'Requisitos de seguridad',
            'min_length' => 'Mínimo 8 caracteres',
            'uppercase' => 'Al menos una letra mayúscula',
            'number' => 'Al menos un número',
            'avoid_common' => 'Evita contraseñas comunes',
        ],
        'strength' => [
            'very_weak' => 'Muy débil',
            'weak' => 'Débil',
            'good' => 'Buena',
            'strong' => 'Fuerte',
            'not_entered' => 'No ingresada',
        ],
    ],

    'verification' => [
        'title' => 'Verificar Correo Electrónico',
        'subtitle' => 'Confirma tu dirección de correo electrónico',
        'sent' => 'Se ha enviado un nuevo enlace de verificación a tu correo electrónico.',
        'check_email' => 'Antes de continuar, por favor revisa tu correo electrónico para el enlace de verificación.',
        'not_received' => 'Si no recibiste el correo',
        'request_another' => 'haz clic aquí para solicitar otro',
    ],

    'errors' => [
        'email_required' => 'El correo electrónico es obligatorio.',
        'email_invalid' => 'Por favor ingresa un correo electrónico válido.',
        'password_required' => 'La contraseña es obligatoria.',
        'password_min' => 'La contraseña debe tener al menos :min caracteres.',
        'password_confirmation' => 'Las contraseñas no coinciden.',
        'name_required' => 'El nombre es obligatorio.',
        'terms_required' => 'Debes aceptar los términos y condiciones.',
    ],

    'success' => [
        'login' => 'Has iniciado sesión exitosamente.',
        'logout' => 'Has cerrado sesión exitosamente.',
        'register' => 'Tu cuenta ha sido creada exitosamente.',
        'password_reset' => 'Tu contraseña ha sido restablecida exitosamente.',
        'password_reset_sent' => 'Se ha enviado el enlace de recuperación a tu correo electrónico.',
        'email_verified' => 'Tu correo electrónico ha sido verificado exitosamente.',
    ],

];
