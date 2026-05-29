<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

/**
 * Autoregistro con correo institucional (@fesc.edu.co) y verificación por enlace.
 */
final class RegistroEstudianteService
{
    /** @return array{0: string, 1: string, 2: ?string} [mensaje, tipo, enlaceDev|null] */
    public static function solicitarRegistro(): array
    {
        if (!defined('REGISTRO_ESTUDIANTE_HABILITADO') || !REGISTRO_ESTUDIANTE_HABILITADO) {
            return ['El autoregistro no está habilitado.', 'warning', null];
        }

        [$err, $datos, $clave] = self::validarPost();
        if ($err !== '') {
            return [$err, 'warning', null];
        }

        $correo = strtolower((string) $datos['correo']);
        $documento = (string) $datos['documento'];

        if (self::existeEstudiante($correo, $documento)) {
            return ['Ya existe un estudiante con ese documento o correo institucional.', 'warning', null];
        }

        self::limpiarExpirados();

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $ahora = fecha_hora_colombia();
        $horas = defined('REGISTRO_TOKEN_HORAS') ? (int) REGISTRO_TOKEN_HORAS : 24;
        $expira = (new \DateTimeImmutable($ahora))->modify('+' . $horas . ' hours')->format('Y-m-d H:i:s');

        $pdo = Database::pdo();
        $pdo->prepare('DELETE FROM registro_estudiante_pendiente WHERE correo = ? OR documento = ?')
            ->execute([$correo, $documento]);

        $json = json_encode($datos, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return ['No se pudieron procesar los datos.', 'warning', null];
        }

        try {
            $pdo->prepare(
                'INSERT INTO registro_estudiante_pendiente
                    (token_hash, correo, documento, datos_json, clave, creado_en, expira_en)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            )->execute([$tokenHash, $correo, $documento, $json, $clave, $ahora, $expira]);
        } catch (\PDOException $e) {
            if (str_contains($e->getMessage(), 'registro_estudiante_pendiente')) {
                return ['Falta la tabla de verificación en la base de datos. Ejecute database/registro_estudiante_verificacion.sql en phpMyAdmin.', 'warning', null];
            }
            throw $e;
        }

        $enlace = url('verificar_registro?token=' . urlencode($token));
        MailService::enviarVerificacionRegistro($correo, $enlace);

        $devEnlace = null;
        if (defined('MAIL_DEV_MOSTRAR_ENLACE') && MAIL_DEV_MOSTRAR_ENLACE) {
            $devEnlace = $_SESSION['_mail_dev_ultimo_enlace'] ?? $enlace;
        }

        return [
            'Revise su correo institucional (' . $correo . ') y abra el enlace de verificación para activar su cuenta.',
            'success',
            $devEnlace,
        ];
    }

    /** @return array{0: string, 1: string} */
    public static function verificarToken(string $token): array
    {
        $token = trim($token);
        if ($token === '' || !preg_match('/^[a-f0-9]{64}$/i', $token)) {
            return ['Enlace de verificación no válido.', 'warning'];
        }

        self::limpiarExpirados();

        $hash = hash('sha256', $token);
        $pdo = Database::pdo();
        $st = $pdo->prepare(
            'SELECT id_pendiente, correo, documento, datos_json, clave, expira_en
             FROM registro_estudiante_pendiente WHERE token_hash = ? LIMIT 1'
        );
        $st->execute([$hash]);
        $row = $st->fetch();
        if (!$row) {
            return ['El enlace no existe o ya fue utilizado.', 'warning'];
        }

        if (strtotime((string) $row['expira_en']) < time()) {
            $pdo->prepare('DELETE FROM registro_estudiante_pendiente WHERE id_pendiente = ?')
                ->execute([(int) $row['id_pendiente']]);

            return ['El enlace expiró. Vuelva a registrarse.', 'warning'];
        }

        $datos = json_decode((string) ($row['datos_json'] ?? ''), true);
        if (!is_array($datos)) {
            return ['Datos de registro corruptos. Intente de nuevo.', 'warning'];
        }

        $correo = strtolower((string) ($row['correo'] ?? ''));
        $documento = (string) ($row['documento'] ?? '');
        if (self::existeEstudiante($correo, $documento)) {
            $pdo->prepare('DELETE FROM registro_estudiante_pendiente WHERE id_pendiente = ?')
                ->execute([(int) $row['id_pendiente']]);

            return ['Ya existe una cuenta con ese documento o correo. Puede iniciar sesión.', 'warning'];
        }

        $estudiantes = load_data('estudiantes');
        $nuevo = [
            'id_estudiante' => next_numeric_id($estudiantes, 'id_estudiante'),
            'tipo_identificacion' => (string) ($datos['tipo_identificacion'] ?? 'CC'),
            'documento' => $documento,
            'nombre' => (string) ($datos['nombre'] ?? ''),
            'apellido' => (string) ($datos['apellido'] ?? ''),
            'correo' => $correo,
            'sexo' => (string) ($datos['sexo'] ?? 'M'),
            'id_programa' => (int) ($datos['id_programa'] ?? 0),
            'programa' => (string) ($datos['programa'] ?? ''),
            'estado_academico' => 'REGULAR',
            'semestre' => (int) ($datos['semestre'] ?? 1),
            'fecha_nacimiento' => (string) ($datos['fecha_nacimiento'] ?? ''),
            'edad' => (int) ($datos['edad'] ?? 0),
            'direccion' => (string) ($datos['direccion'] ?? ''),
            'barrio' => (string) ($datos['barrio'] ?? ''),
            'telefono' => (string) ($datos['telefono'] ?? ''),
            'id_sede' => (int) ($datos['id_sede'] ?? 1),
            'id_jornada' => (int) ($datos['id_jornada'] ?? 1),
            'clave' => (string) ($row['clave'] ?? ''),
        ];
        $estudiantes[] = $nuevo;
        save_data('estudiantes', $estudiantes);

        $pdo->prepare('DELETE FROM registro_estudiante_pendiente WHERE id_pendiente = ?')
            ->execute([(int) $row['id_pendiente']]);

        return ['Cuenta activada correctamente. Ya puede iniciar sesión con su correo institucional o documento.', 'success'];
    }

    /** @return array{0: string, 1: array<string, mixed>, 2: string} */
    private static function validarPost(): array
    {
        $tiposPerm = array_column(diccionario_tipos_identificacion(), 'codigo');
        $tipoId = (string) post('tipo_identificacion', '');
        $sexo = (string) post('sexo', '');
        $sexosPerm = array_column(diccionario_sexo(), 'codigo');
        $idProg = (int) post('id_programa', '0');
        $sem = (int) post('semestre', '1');
        $fn = trim((string) post('fecha_nacimiento', ''));
        $correo = strtolower(trim((string) post('correo', '')));
        $clavePost = (string) post('clave', '');
        $claveConf = (string) post('clave_confirmar', '');
        $minClave = defined('REGISTRO_CLAVE_MIN') ? (int) REGISTRO_CLAVE_MIN : 8;

        if (!in_array($tipoId, $tiposPerm, true)) {
            return ['Seleccione el tipo de identificación.', [], ''];
        }
        if (trim((string) post('documento', '')) === '') {
            return ['Ingrese el número de identificación.', [], ''];
        }
        if (trim((string) post('nombre', '')) === '' || trim((string) post('apellido', '')) === '') {
            return ['Ingrese nombres y apellidos.', [], ''];
        }
        if ($correo === '') {
            return ['Ingrese su correo institucional.', [], ''];
        }
        if (!correo_es_institucional_estudiante($correo)) {
            $dominios = defined('REGISTRO_DOMINIOS_CORREO') ? implode(', @', REGISTRO_DOMINIOS_CORREO) : 'fesc.edu.co';

            return ['Use su correo universitario (@' . $dominios . ').', [], ''];
        }
        if (!in_array($sexo, $sexosPerm, true)) {
            return ['Seleccione el sexo.', [], ''];
        }
        if ($idProg <= 0) {
            return ['Seleccione la carrera.', [], ''];
        }
        if ($sem < 1 || $sem > 10) {
            return ['Seleccione un semestre entre 1 y 10.', [], ''];
        }
        if ($fn === '') {
            return ['Indique la fecha de nacimiento.', [], ''];
        }
        if (trim((string) post('direccion', '')) === '') {
            return ['Ingrese la dirección.', [], ''];
        }
        if (trim((string) post('barrio', '')) === '') {
            return ['Ingrese el barrio.', [], ''];
        }
        if (trim((string) post('telefono', '')) === '') {
            return ['Ingrese el teléfono.', [], ''];
        }
        if ($clavePost === '' || strlen($clavePost) < $minClave) {
            return ['La contraseña debe tener al menos ' . $minClave . ' caracteres.', [], ''];
        }
        if ($clavePost !== $claveConf) {
            return ['Las contraseñas no coinciden.', [], ''];
        }

        $datos = [
            'tipo_identificacion' => $tipoId,
            'documento' => trim((string) post('documento', '')),
            'nombre' => trim((string) post('nombre', '')),
            'apellido' => trim((string) post('apellido', '')),
            'correo' => $correo,
            'sexo' => $sexo,
            'id_programa' => $idProg,
            'programa' => programa_label_by_id($idProg),
            'semestre' => $sem,
            'fecha_nacimiento' => $fn,
            'edad' => calcular_edad_desde_fecha_ymd($fn),
            'direccion' => trim((string) post('direccion', '')),
            'barrio' => trim((string) post('barrio', '')),
            'telefono' => trim((string) post('telefono', '')),
            'id_sede' => (int) post('id_sede', '1'),
            'id_jornada' => (int) post('id_jornada', '1'),
        ];

        return ['', $datos, $clavePost];
    }

    private static function existeEstudiante(string $correo, string $documento): bool
    {
        foreach (load_data('estudiantes') as $e) {
            if ((string) ($e['documento'] ?? '') === $documento) {
                return true;
            }
            if (strcasecmp((string) ($e['correo'] ?? ''), $correo) === 0) {
                return true;
            }
        }

        return false;
    }

    private static function limpiarExpirados(): void
    {
        try {
            Database::pdo()->exec(
                'DELETE FROM registro_estudiante_pendiente WHERE expira_en < NOW()'
            );
        } catch (\Throwable) {
            // Tabla aún no importada: el INSERT fallará con mensaje claro al usuario.
        }
    }
}
