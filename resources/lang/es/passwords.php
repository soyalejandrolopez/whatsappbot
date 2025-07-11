<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'reset' => '¡Tu contraseña ha sido restablecida exitosamente!',
    'sent' => '¡Hemos enviado el enlace de recuperación de contraseña a tu correo electrónico!',
    'throttled' => 'Por favor espera antes de volver a intentarlo.',
    'token' => 'Este enlace de restablecimiento de contraseña es inválido o ha expirado.',
    'user' => 'No podemos encontrar un usuario con esa dirección de correo electrónico.',

    // Additional password messages
    'email' => [
        'subject' => 'Restablecer Contraseña - ChatBot WhatsApp',
        'greeting' => '¡Hola!',
        'line1' => 'Recibiste este correo porque solicitaste restablecer la contraseña de tu cuenta.',
        'action' => 'Restablecer Contraseña',
        'line2' => 'Este enlace de restablecimiento expirará en :count minutos.',
        'line3' => 'Si no solicitaste restablecer tu contraseña, no es necesario realizar ninguna acción.',
        'salutation' => 'Saludos,<br>El equipo de ChatBot WhatsApp',
        'trouble' => 'Si tienes problemas para hacer clic en el botón "Restablecer Contraseña", copia y pega la siguiente URL en tu navegador web:',
    ],

    'form' => [
        'email_placeholder' => 'Ingresa tu correo electrónico',
        'password_placeholder' => 'Ingresa tu nueva contraseña',
        'password_confirm_placeholder' => 'Confirma tu nueva contraseña',
        'submit_reset' => 'Enviar Enlace de Recuperación',
        'submit_update' => 'Restablecer Contraseña',
        'back_to_login' => 'Volver al inicio de sesión',
    ],

    'validation' => [
        'email_required' => 'El correo electrónico es obligatorio.',
        'email_invalid' => 'Por favor ingresa un correo electrónico válido.',
        'password_required' => 'La contraseña es obligatoria.',
        'password_min' => 'La contraseña debe tener al menos :min caracteres.',
        'password_confirmed' => 'La confirmación de contraseña no coincide.',
        'token_invalid' => 'El enlace de restablecimiento es inválido o ha expirado.',
    ],

    'success' => [
        'sent' => 'Se ha enviado el enlace de recuperación a tu correo electrónico. Por favor revisa tu bandeja de entrada y carpeta de spam.',
        'reset' => 'Tu contraseña ha sido restablecida exitosamente. Ya puedes iniciar sesión con tu nueva contraseña.',
    ],

    'info' => [
        'response_time' => 'El enlace de recuperación llegará a tu correo en menos de 5 minutos.',
        'check_spam' => 'Si no lo recibes, revisa tu carpeta de spam.',
        'link_expires' => 'Este enlace expirará en 60 minutos por seguridad.',
        'security_notice' => 'Si no solicitaste este cambio, ignora este mensaje.',
    ],

];
