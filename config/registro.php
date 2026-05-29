<?php
declare(strict_types=1);

/** Autoregistro de estudiantes con correo institucional verificado. */
define('REGISTRO_ESTUDIANTE_HABILITADO', true);

/** Dominios permitidos (solo el host, sin @). */
define('REGISTRO_DOMINIOS_CORREO', ['fesc.edu.co']);

/** Validez del enlace de verificación (horas). */
define('REGISTRO_TOKEN_HORAS', 24);

/** Longitud mínima de contraseña en autoregistro. */
define('REGISTRO_CLAVE_MIN', 8);

define('MAIL_FROM', 'noreply@fesc.edu.co');
define('MAIL_FROM_NAME', 'Sistema de solicitudes académicas');

/**
 * En XAMPP/local: muestra el enlace de verificación en pantalla (sin SMTP).
 * En producción: false y configurar envío real en MailService.
 */
define('MAIL_DEV_MOSTRAR_ENLACE', true);
