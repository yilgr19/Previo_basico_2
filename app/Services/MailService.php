<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Envío de correo. En local/XAMPP puede mostrar el enlace en pantalla (MAIL_DEV_MOSTRAR_ENLACE).
 */
final class MailService
{
    public static function enviarVerificacionRegistro(string $correo, string $enlaceVerificacion): bool
    {
        $asunto = 'Verifique su correo — ' . (defined('SITE_APP_NAME') ? SITE_APP_NAME : 'Sistema académico');
        $cuerpo = "Hola,\n\n"
            . "Recibimos su solicitud de registro como estudiante.\n"
            . "Para activar su cuenta, abra este enlace (válido "
            . (defined('REGISTRO_TOKEN_HORAS') ? (string) REGISTRO_TOKEN_HORAS : '24')
            . " horas):\n\n"
            . $enlaceVerificacion . "\n\n"
            . "Si usted no solicitó este registro, ignore este mensaje.\n\n"
            . (defined('SITE_APP_NAME') ? SITE_APP_NAME : 'Sistema académico');

        if (defined('MAIL_DEV_MOSTRAR_ENLACE') && MAIL_DEV_MOSTRAR_ENLACE) {
            $_SESSION['_mail_dev_ultimo_enlace'] = $enlaceVerificacion;

            return true;
        }

        $from = defined('MAIL_FROM') ? MAIL_FROM : 'noreply@localhost';
        $headers = 'From: ' . $from . "\r\n" . 'Content-Type: text/plain; charset=UTF-8';

        return @mail($correo, $asunto, $cuerpo, $headers);
    }
}
