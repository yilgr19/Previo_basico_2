<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Registro y gestión de solicitudes (JSON + adjuntos).
 */
final class SolicitudesService
{
    /** @return array{0: string, 1: string} mensaje y tipo */
    public static function registrarDesdeEstudiante(int $idEstudiante): array
    {
        $est = repo_estudiante_por_id($idEstudiante);
        if (!$est) {
            return ['Sesión inválida.', 'warning'];
        }

        $idTipo = (int) post('id_tipo_solicitud', '0');
        $desc = trim((string) post('descripcion', ''));
        $docDoc = preg_replace('/\D/', '', trim((string) post('documento_docente_relacionado', '')));

        $tipo = tipo_solicitud_por_id($idTipo);
        if (!$tipo) {
            return ['Seleccione un tipo de solicitud válido.', 'warning'];
        }
        $len = function_exists('mb_strlen') ? mb_strlen($desc, 'UTF-8') : strlen($desc);
        if ($desc === '' || $len < 10) {
            return ['Describa su solicitud con al menos 10 caracteres.', 'warning'];
        }

        $rows = load_data('solicitudes');
        $idSol = next_numeric_id($rows, 'id_solicitud');
        [$anexos, $errAnexos] = SolicitudesAnexosUpload::guardarParaSolicitud($idSol);
        if ($errAnexos !== null) {
            return [$errAnexos, 'warning'];
        }

        $row = [
            'id_solicitud' => $idSol,
            'id_estudiante' => $idEstudiante,
            'id_docente_solicitante' => 0,
            'documento_estudiante' => (string) ($est['documento'] ?? ''),
            'id_tipo_solicitud' => $idTipo,
            'codigo_tipo' => (string) ($tipo['codigo'] ?? ''),
            'fecha_registro' => date('Y-m-d'),
            'estado' => 'pendiente',
            'descripcion' => $desc,
            'documento_docente_relacionado' => $docDoc,
            'respuesta' => '',
            'fecha_respuesta' => '',
            'anexos_archivos' => $anexos,
        ];
        $rows[] = $row;
        save_data('solicitudes', $rows);

        return ['Solicitud registrada correctamente.', 'success'];
    }

    /** @return array{0: string, 1: string} */
    public static function registrarDesdeDocente(int $idDocente): array
    {
        $doc = repo_docente_por_id($idDocente);
        if (!$doc) {
            return ['Sesión inválida.', 'warning'];
        }

        $idTipo = (int) post('id_tipo_solicitud', '0');
        $desc = trim((string) post('descripcion', ''));
        $docRel = preg_replace('/\D/', '', trim((string) post('documento_docente_relacionado', '')));

        $tipo = tipo_solicitud_por_id($idTipo);
        if (!$tipo) {
            return ['Seleccione un tipo de solicitud válido.', 'warning'];
        }
        $len = function_exists('mb_strlen') ? mb_strlen($desc, 'UTF-8') : strlen($desc);
        if ($desc === '' || $len < 10) {
            return ['Describa su solicitud con al menos 10 caracteres.', 'warning'];
        }

        $rows = load_data('solicitudes');
        $idSol = next_numeric_id($rows, 'id_solicitud');
        [$anexos, $errAnexos] = SolicitudesAnexosUpload::guardarParaSolicitud($idSol);
        if ($errAnexos !== null) {
            return [$errAnexos, 'warning'];
        }

        $row = [
            'id_solicitud' => $idSol,
            'id_estudiante' => 0,
            'id_docente_solicitante' => $idDocente,
            'documento_estudiante' => '',
            'id_tipo_solicitud' => $idTipo,
            'codigo_tipo' => (string) ($tipo['codigo'] ?? ''),
            'fecha_registro' => date('Y-m-d'),
            'estado' => 'pendiente',
            'descripcion' => $desc,
            'documento_docente_relacionado' => $docRel,
            'respuesta' => '',
            'fecha_respuesta' => '',
            'anexos_archivos' => $anexos,
        ];
        $rows[] = $row;
        save_data('solicitudes', $rows);

        return ['Solicitud registrada correctamente.', 'success'];
    }

    /** @return array{0: string, 1: string} */
    public static function actualizarEstadoAdmin(int $idSolicitud, string $nuevoEstado, string $respuesta): array
    {
        if (!in_array($nuevoEstado, solicitud_codigos_estado_validos(), true)) {
            return ['Estado no válido.', 'warning'];
        }
        $rows = load_data('solicitudes');
        $found = false;
        foreach ($rows as &$s) {
            if ((int) ($s['id_solicitud'] ?? 0) === $idSolicitud) {
                $s['estado'] = $nuevoEstado;
                $s['respuesta'] = trim($respuesta);
                $s['fecha_respuesta'] = date('Y-m-d');
                $found = true;
                break;
            }
        }
        unset($s);
        if (!$found) {
            return ['Solicitud no encontrada.', 'warning'];
        }
        save_data('solicitudes', $rows);

        return ['Solicitud actualizada.', 'success'];
    }

    /**
     * @param array{fecha_desde?: string, fecha_hasta?: string, estado?: string, aprobacion?: string, buscar?: string} $f
     * @return list<array{solicitud: array, estudiante: ?array, docente_solicitante: ?array}>
     */
    public static function listadoParaAdmin(array $f): array
    {
        $fd = trim((string) ($f['fecha_desde'] ?? ''));
        $fh = trim((string) ($f['fecha_hasta'] ?? ''));
        $est = trim((string) ($f['estado'] ?? ''));
        $aprob = trim((string) ($f['aprobacion'] ?? ''));
        $bus = trim((string) ($f['buscar'] ?? ''));
        $busNorm = preg_replace('/\s+/', '', $bus);

        $rows = load_data('solicitudes');
        $out = [];

        foreach ($rows as $s) {
            $s = self::normalizarLegacy($s);
            $fr = (string) ($s['fecha_registro'] ?? '');
            if ($fd !== '' && $fr !== '' && strcmp($fr, $fd) < 0) {
                continue;
            }
            if ($fh !== '' && $fr !== '' && strcmp($fr, $fh) > 0) {
                continue;
            }
            if ($est !== '' && (string) ($s['estado'] ?? '') !== $est) {
                continue;
            }
            $cod = (string) ($s['estado'] ?? '');
            $esAprobada = $cod === 'aprobada';
            if ($aprob === 'aprobadas' && !$esAprobada) {
                continue;
            }
            if ($aprob === 'no_aprobadas' && $esAprobada) {
                continue;
            }

            $idEst = (int) ($s['id_estudiante'] ?? 0);
            $idDocSol = (int) ($s['id_docente_solicitante'] ?? 0);
            $estudiante = $idEst > 0 ? repo_estudiante_por_id($idEst) : null;
            $docSol = $idDocSol > 0 ? repo_docente_por_id($idDocSol) : null;

            if ($busNorm !== '') {
                $docE = preg_replace('/\D/', '', (string) ($s['documento_estudiante'] ?? ''));
                $docEd = $estudiante ? preg_replace('/\D/', '', (string) ($estudiante['documento'] ?? '')) : '';
                $docProf = preg_replace('/\D/', '', (string) ($s['documento_docente_relacionado'] ?? ''));
                $docDocenteRad = $docSol ? preg_replace('/\D/', '', (string) ($docSol['documento'] ?? '')) : '';
                $hay = str_contains($docE, $busNorm) || str_contains($docEd, $busNorm)
                    || str_contains($docProf, $busNorm) || str_contains($docDocenteRad, $busNorm);
                if (!$hay && $estudiante) {
                    $nombre = strtolower((string) (($estudiante['nombre'] ?? '') . ' ' . ($estudiante['apellido'] ?? '')));
                    if (!str_contains($nombre, strtolower($bus))) {
                        continue;
                    }
                } elseif (!$hay && $docSol) {
                    $nombre = strtolower((string) (($docSol['nombre'] ?? '') . ' ' . ($docSol['apellido'] ?? '')));
                    if (!str_contains($nombre, strtolower($bus))) {
                        continue;
                    }
                } elseif (!$hay && !$estudiante && !$docSol) {
                    continue;
                }
            }

            $out[] = [
                'solicitud' => $s,
                'estudiante' => $estudiante,
                'docente_solicitante' => $docSol,
            ];
        }

        usort($out, static function ($a, $b) {
            $ia = (int) ($a['solicitud']['id_solicitud'] ?? 0);
            $ib = (int) ($b['solicitud']['id_solicitud'] ?? 0);

            return $ib <=> $ia;
        });

        return $out;
    }

    /**
     * Solicitudes radicadas por estudiante donde el docente aparece en documento_docente_relacionado.
     * Vista sin datos identificables del estudiante (uso en panel docente).
     *
     * @return list<array{solicitud: array, vista_anonima: true}>
     */
    public static function listadoMencionesAnonimasParaDocente(int $idDocente, string $documentoDocente): array
    {
        $docNorm = preg_replace('/\D/', '', $documentoDocente);
        if ($docNorm === '') {
            return [];
        }

        $rows = load_data('solicitudes');
        $out = [];
        foreach ($rows as $s) {
            $s = self::normalizarLegacy($s);
            $idEst = (int) ($s['id_estudiante'] ?? 0);
            if ($idEst <= 0) {
                continue;
            }
            $rel = preg_replace('/\D/', '', (string) ($s['documento_docente_relacionado'] ?? ''));
            if ($rel === '' || $rel !== $docNorm) {
                continue;
            }
            $out[] = ['solicitud' => $s, 'vista_anonima' => true];
        }

        usort($out, static function ($a, $b) {
            return ((int) ($b['solicitud']['id_solicitud'] ?? 0)) <=> ((int) ($a['solicitud']['id_solicitud'] ?? 0));
        });

        return $out;
    }

    /** Si el usuario puede descargar anexos de esta solicitud (no aplica a vista mención anónima). */
    public static function usuarioPuedeVerAnexos(?array $user, array $solicitud): bool
    {
        if (!$user) {
            return false;
        }
        $rol = (string) ($user['rol'] ?? '');
        if ($rol === \ROLE_ADMIN) {
            return true;
        }
        $idEst = (int) ($solicitud['id_estudiante'] ?? 0);
        $idDoc = (int) ($solicitud['id_docente_solicitante'] ?? 0);
        if ($rol === \ROLE_ESTUDIANTE && $idEst > 0 && (int) ($user['id'] ?? 0) === $idEst) {
            return true;
        }
        if ($rol === \ROLE_DOCENTE && $idDoc > 0 && (int) ($user['id'] ?? 0) === $idDoc) {
            return true;
        }

        return false;
    }

    /** Compatibilidad con registros antiguos (texto libre en estado). */
    private static function normalizarLegacy(array $s): array
    {
        if (!isset($s['id_docente_solicitante'])) {
            $s['id_docente_solicitante'] = 0;
        }
        if (!isset($s['anexos_archivos']) || !is_array($s['anexos_archivos'])) {
            $s['anexos_archivos'] = [];
        }
        $e = strtolower(trim((string) ($s['estado'] ?? '')));
        $map = [
            'en revisión' => 'en_revision',
            'en revision' => 'en_revision',
            'pendiente' => 'pendiente',
        ];
        if (isset($map[$e])) {
            $s['estado'] = $map[$e];
        }

        return $s;
    }
}
