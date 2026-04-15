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
        $tipo = tipo_solicitud_por_id($idTipo);
        if (!$tipo) {
            return ['Seleccione un tipo de solicitud válido.', 'warning'];
        }

        $periodo = trim((string) post('periodo_academico', ''));
        if (!preg_match('/^\d{4}-\d{1,2}$/', $periodo)) {
            return ['Indique el periodo académico en formato AAAA-S (ej. 2026-1).', 'warning'];
        }

        $idSedeSol = (int) post('id_sede_solicitud', '0');
        $idJornadaSol = (int) post('id_jornada_solicitud', '0');
        if ($idSedeSol <= 0 || sede_nombre($idSedeSol) === '') {
            return ['Seleccione la sede a la que aplica la solicitud.', 'warning'];
        }
        if ($idJornadaSol <= 0 || jornada_nombre($idJornadaSol) === '') {
            return ['Seleccione la jornada.', 'warning'];
        }

        $motivo = strtolower(trim((string) post('motivo_solicitud', '')));
        $motivosOk = array_column(diccionario_motivos_solicitud_estudiante(), 'codigo');
        if (!in_array($motivo, $motivosOk, true)) {
            return ['Seleccione el motivo de la solicitud.', 'warning'];
        }

        $exposicion = trim((string) post('exposicion', ''));
        $len = function_exists('mb_strlen') ? mb_strlen($exposicion, 'UTF-8') : strlen($exposicion);
        if ($exposicion === '' || $len < 10) {
            return ['La exposición de motivos debe tener al menos 10 caracteres.', 'warning'];
        }

        if (post('consentimiento_veracidad', '') !== '1') {
            return ['Debe aceptar la declaración de veracidad y el conocimiento del reglamento.', 'warning'];
        }

        $idProg = (int) ($est['id_programa'] ?? 0);

        if ($motivo === 'salud' && !SolicitudesAnexosUpload::hayArchivoSubidoOk('soporte_medico')) {
            return ['Si el motivo es salud, debe adjuntar soporte médico.', 'warning'];
        }
        if (in_array($idTipo, [5, 9], true) && !SolicitudesAnexosUpload::hayArchivoSubidoOk('carta_aceptacion')) {
            return ['Para transferencia o traslado de sede adjunte la carta de aceptación u orden correspondiente.', 'warning'];
        }
        if (in_array($idTipo, [10, 11, 12], true) && !SolicitudesAnexosUpload::hayArchivoSubidoOk('recibo_pago')) {
            return ['Para este trámite con costo administrativo adjunte el recibo de pago.', 'warning'];
        }

        $docDoc = preg_replace('/\D/', '', trim((string) post('documento_docente_relacionado', '')));

        $rows = load_data('solicitudes');
        $idSol = next_numeric_id($rows, 'id_solicitud');
        [$anexos, $errAnexos] = SolicitudesAnexosUpload::guardarMultiplesCampos($idSol, [
            ['input' => 'anexos', 'categoria' => 'general', 'multiple' => true],
            ['input' => 'soporte_medico', 'categoria' => 'soporte_medico', 'multiple' => false],
            ['input' => 'carta_aceptacion', 'categoria' => 'carta_aceptacion', 'multiple' => false],
            ['input' => 'recibo_pago', 'categoria' => 'recibo_pago', 'multiple' => false],
        ]);
        if ($errAnexos !== null) {
            return [$errAnexos, 'warning'];
        }

        $estadoAcad = strtoupper(trim((string) ($est['estado_academico'] ?? 'REGULAR')));
        $sem = (int) ($est['semestre'] ?? 0);

        $detalleEst = [
            'perfil_snapshot' => [
                'id_estudiantil' => (string) ($est['documento'] ?? ''),
                'id_programa' => $idProg,
                'programa_nombre' => programa_label_by_id($idProg),
                'estado_academico' => $estadoAcad,
                'estado_academico_label' => estado_academico_estudiante_nombre($estadoAcad),
                'semestre' => $sem,
                'id_sede_matricula' => (int) ($est['id_sede'] ?? 0),
                'id_jornada_matricula' => (int) ($est['id_jornada'] ?? 0),
            ],
            'clasificacion' => [
                'periodo_academico' => $periodo,
                'id_sede_solicitud' => $idSedeSol,
                'id_jornada_solicitud' => $idJornadaSol,
            ],
            'cuerpo' => [
                'motivo' => $motivo,
                'motivo_label' => motivo_solicitud_estudiante_nombre($motivo),
                'exposicion' => $exposicion,
            ],
            'consentimientos' => [
                'veracidad' => true,
            ],
        ];

        $row = [
            'id_solicitud' => $idSol,
            'id_estudiante' => $idEstudiante,
            'id_docente_solicitante' => 0,
            'documento_estudiante' => (string) ($est['documento'] ?? ''),
            'id_tipo_solicitud' => $idTipo,
            'id_tipo_solicitud_docente' => 0,
            'codigo_tipo' => (string) ($tipo['codigo'] ?? ''),
            'fecha_registro' => date('Y-m-d'),
            'estado' => 'pendiente',
            'descripcion' => $exposicion,
            'documento_docente_relacionado' => $docDoc,
            'respuesta' => '',
            'fecha_respuesta' => '',
            'respondido_en' => '',
            'respuesta_elaborada' => null,
            'anexos_archivos' => $anexos,
            'detalle_estudiante' => $detalleEst,
            'detalle_docente' => null,
            'formulario_version' => 2,
            'notif_pendiente_est' => false,
            'notif_pendiente_doc' => false,
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

        $idTipoDoc = (int) post('id_tipo_solicitud_docente', '0');
        $tipoDoc = tipo_solicitud_docente_por_id($idTipoDoc);
        if (!$tipoDoc) {
            return ['Seleccione un tipo de solicitud válido (catálogo docente).', 'warning'];
        }

        $asunto = trim((string) post('asunto', ''));
        if (function_exists('mb_strlen') ? mb_strlen($asunto, 'UTF-8') : strlen($asunto) < 3) {
            return ['Indique un asunto breve (mínimo 3 caracteres).', 'warning'];
        }

        $prioridad = strtolower(trim((string) post('prioridad', '')));
        $priOk = array_column(diccionario_prioridad_solicitud_docente(), 'codigo');
        if (!in_array($prioridad, $priOk, true)) {
            return ['Seleccione el nivel de prioridad.', 'warning'];
        }

        $desc = trim((string) post('descripcion_detallada', ''));
        $len = function_exists('mb_strlen') ? mb_strlen($desc, 'UTF-8') : strlen($desc);
        if ($desc === '' || $len < 10) {
            return ['La descripción detallada debe tener al menos 10 caracteres.', 'warning'];
        }

        $sustento = trim((string) post('sustento_legal', ''));
        $nrc = trim((string) post('nrc', ''));
        $nomMat = trim((string) post('nombre_materia', ''));
        $horarioImp = trim((string) post('horario_impactado', ''));
        $planCont = trim((string) post('plan_contingencia', ''));

        $fi = trim((string) post('fecha_inicio', ''));
        $ff = trim((string) post('fecha_fin', ''));
        if ($fi === '' || $ff === '') {
            return ['Indique la fecha de inicio y la fecha de fin del requerimiento.', 'warning'];
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fi) !== 1 || preg_match('/^\d{4}-\d{2}-\d{2}$/', $ff) !== 1) {
            return ['Use fechas en formato AAAA-MM-DD.', 'warning'];
        }
        if (strcmp($ff, $fi) < 0) {
            return ['La fecha de fin no puede ser anterior a la de inicio.', 'warning'];
        }

        if (post('consentimiento_responsabilidad', '') !== '1') {
            return ['Debe aceptar la declaración de responsabilidad sobre la carga académica.', 'warning'];
        }

        $docRel = preg_replace('/\D/', '', trim((string) post('documento_docente_relacionado', '')));

        $rows = load_data('solicitudes');
        $idSol = next_numeric_id($rows, 'id_solicitud');
        [$anexos, $errAnexos] = SolicitudesAnexosUpload::guardarMultiplesCampos($idSol, [
            ['input' => 'anexos', 'categoria' => 'general', 'multiple' => true],
            ['input' => 'anexos_terceros', 'categoria' => 'doc_terceros', 'multiple' => true],
            ['input' => 'anexos_formatos', 'categoria' => 'formato_institucional', 'multiple' => true],
        ]);
        if ($errAnexos !== null) {
            return [$errAnexos, 'warning'];
        }

        $idEmp = trim((string) ($doc['codigo_empleado'] ?? ''));
        if ($idEmp === '') {
            $idEmp = (string) ($doc['documento'] ?? '');
        }
        $detalleDoc = [
            'perfil_snapshot' => [
                'id_empleado' => $idEmp,
                'unidad_academica' => trim((string) ($doc['unidad_academica'] ?? '')),
                'categoria_docente' => strtolower(trim((string) ($doc['categoria_docente'] ?? ''))),
                'categoria_docente_label' => categoria_docente_nombre((string) ($doc['categoria_docente'] ?? '')),
                'tipo_contrato' => strtolower(trim((string) ($doc['tipo_contrato'] ?? ''))),
                'tipo_contrato_label' => tipo_contrato_docente_nombre((string) ($doc['tipo_contrato'] ?? '')),
                'documento' => (string) ($doc['documento'] ?? ''),
                'nombre_completo' => trim(($doc['nombre'] ?? '') . ' ' . ($doc['apellido'] ?? '')),
            ],
            'clasificacion' => [
                'asunto' => $asunto,
                'prioridad' => $prioridad,
                'prioridad_label' => prioridad_solicitud_docente_nombre($prioridad),
            ],
            'carga_afectada' => [
                'nrc' => $nrc,
                'nombre_materia' => $nomMat,
                'horario_impactado' => $horarioImp,
                'plan_contingencia' => $planCont,
            ],
            'cuerpo' => [
                'descripcion_detallada' => $desc,
                'sustento_legal' => $sustento,
                'fecha_inicio' => $fi,
                'fecha_fin' => $ff,
            ],
            'consentimientos' => [
                'responsabilidad' => true,
            ],
        ];

        $row = [
            'id_solicitud' => $idSol,
            'id_estudiante' => 0,
            'id_docente_solicitante' => $idDocente,
            'documento_estudiante' => '',
            'id_tipo_solicitud' => 0,
            'id_tipo_solicitud_docente' => $idTipoDoc,
            'codigo_tipo' => (string) ($tipoDoc['codigo'] ?? ''),
            'fecha_registro' => date('Y-m-d'),
            'estado' => 'pendiente',
            'descripcion' => $desc,
            'documento_docente_relacionado' => $docRel,
            'respuesta' => '',
            'fecha_respuesta' => '',
            'respondido_en' => '',
            'respuesta_elaborada' => null,
            'anexos_archivos' => $anexos,
            'detalle_estudiante' => null,
            'detalle_docente' => $detalleDoc,
            'formulario_version' => 2,
            'notif_pendiente_est' => false,
            'notif_pendiente_doc' => false,
        ];
        $rows[] = $row;
        save_data('solicitudes', $rows);

        return ['Solicitud registrada correctamente.', 'success'];
    }

    /** @return array{0: string, 1: string} */
    public static function actualizarEstadoAdmin(int $idSolicitud, string $nuevoEstado, string $respuesta, bool $guardarRespuestaElaborada = false): array
    {
        if (!in_array($nuevoEstado, solicitud_codigos_estado_validos(), true)) {
            return ['Estado no válido.', 'warning'];
        }
        $rows = load_data('solicitudes');
        $found = false;
        foreach ($rows as &$s) {
            if ((int) ($s['id_solicitud'] ?? 0) === $idSolicitud) {
                $s = self::normalizarLegacy($s);
                if (solicitud_tiene_respuesta_cerrada($s)) {
                    return ['Esta solicitud ya fue respondida por la universidad. No se permiten nuevas modificaciones.', 'warning'];
                }
                $respTrim = trim($respuesta);
                $cierraRespuesta = $respTrim !== '' || $guardarRespuestaElaborada;
                $ahora = fecha_hora_colombia();

                $s['estado'] = $nuevoEstado;
                $s['respuesta'] = $respTrim;
                if ($cierraRespuesta) {
                    $s['respondido_en'] = $ahora;
                    $s['fecha_respuesta'] = substr($ahora, 0, 10);
                }
                if ($guardarRespuestaElaborada) {
                    $prev = is_array($s['respuesta_elaborada'] ?? null) ? $s['respuesta_elaborada'] : null;
                    $s['respuesta_elaborada'] = solicitud_respuesta_elaborada_desde_post($idSolicitud, $prev, $ahora);
                }
                $idEst = (int) ($s['id_estudiante'] ?? 0);
                $idDocSol = (int) ($s['id_docente_solicitante'] ?? 0);
                if ($idEst > 0) {
                    $s['notif_pendiente_est'] = true;
                }
                if ($idDocSol > 0) {
                    $s['notif_pendiente_doc'] = true;
                }
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
     * @param array{fecha_desde?: string, fecha_hasta?: string, estado?: string, aprobacion?: string, buscar?: string, radicante?: string, id_sede?: int} $f
     * radicante: ''|'todos' — todas; 'estudiantes' — id_estudiante > 0; 'docentes' — radicadas por docente (id_docente_solicitante > 0 e id_estudiante == 0).
     * id_sede: si &gt; 0, solo solicitudes cuya sede de gestión coincide (trámite estudiantil o sede del docente radicante).
     * @return list<array{solicitud: array, estudiante: ?array, docente_solicitante: ?array}>
     */
    public static function listadoParaAdmin(array $f): array
    {
        $fd = trim((string) ($f['fecha_desde'] ?? ''));
        $fh = trim((string) ($f['fecha_hasta'] ?? ''));
        $est = trim((string) ($f['estado'] ?? ''));
        $aprob = trim((string) ($f['aprobacion'] ?? ''));
        $rad = trim((string) ($f['radicante'] ?? ''));
        if ($rad !== 'estudiantes' && $rad !== 'docentes') {
            $rad = '';
        }
        $bus = trim((string) ($f['buscar'] ?? ''));
        $busNorm = preg_replace('/\s+/', '', $bus);
        $idSedeFiltro = (int) ($f['id_sede'] ?? 0);

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
            if ($rad === 'estudiantes' && $idEst <= 0) {
                continue;
            }
            if ($rad === 'docentes' && ($idDocSol <= 0 || $idEst > 0)) {
                continue;
            }
            $estudiante = $idEst > 0 ? repo_estudiante_por_id($idEst) : null;
            $docSol = $idDocSol > 0 ? repo_docente_por_id($idDocSol) : null;

            if ($idSedeFiltro > 0) {
                $sedeSol = solicitud_sede_para_bandera_gestion($s, $estudiante, $docSol);
                if ($sedeSol !== $idSedeFiltro) {
                    continue;
                }
            }

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
            $fa = (string) ($a['solicitud']['fecha_registro'] ?? '');
            $fb = (string) ($b['solicitud']['fecha_registro'] ?? '');
            if ($fa === '') {
                $fa = '0000-00-00';
            }
            if ($fb === '') {
                $fb = '0000-00-00';
            }
            $cmp = strcmp($fb, $fa);
            if ($cmp !== 0) {
                return $cmp;
            }
            $ia = (int) ($a['solicitud']['id_solicitud'] ?? 0);
            $ib = (int) ($b['solicitud']['id_solicitud'] ?? 0);

            return $ib <=> $ia;
        });

        return $out;
    }

    /** Comprueba si la solicitud corresponde a la sede de la bandeja (misma regla que listadoParaAdmin). */
    public static function solicitudPerteneceASedeBandeja(int $idSolicitud, int $idSedeRequerida): bool
    {
        if ($idSedeRequerida <= 0) {
            return true;
        }
        $rows = load_data('solicitudes');
        foreach ($rows as $s) {
            if ((int) ($s['id_solicitud'] ?? 0) !== $idSolicitud) {
                continue;
            }
            $s = self::normalizarLegacy($s);
            $idEst = (int) ($s['id_estudiante'] ?? 0);
            $idDocSol = (int) ($s['id_docente_solicitante'] ?? 0);
            $est = $idEst > 0 ? repo_estudiante_por_id($idEst) : null;
            $docSol = $idDocSol > 0 ? repo_docente_por_id($idDocSol) : null;

            return solicitud_sede_para_bandera_gestion($s, $est, $docSol) === $idSedeRequerida;
        }

        return false;
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

    /** Normaliza claves para vistas y listados (registros antiguos). */
    public static function normalizarParaVista(array $s): array
    {
        return self::normalizarLegacy($s);
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
        if (!isset($s['id_tipo_solicitud_docente'])) {
            $s['id_tipo_solicitud_docente'] = 0;
        }
        if (!array_key_exists('detalle_estudiante', $s)) {
            $s['detalle_estudiante'] = null;
        }
        if (!array_key_exists('detalle_docente', $s)) {
            $s['detalle_docente'] = null;
        }
        if (!isset($s['formulario_version'])) {
            $s['formulario_version'] = 1;
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
        if (!array_key_exists('notif_pendiente_est', $s)) {
            $s['notif_pendiente_est'] = false;
        }
        if (!array_key_exists('notif_pendiente_doc', $s)) {
            $s['notif_pendiente_doc'] = false;
        }
        if (!array_key_exists('respuesta_elaborada', $s)) {
            $s['respuesta_elaborada'] = null;
        } elseif (is_string($s['respuesta_elaborada'])) {
            $dec = json_decode($s['respuesta_elaborada'], true);
            $s['respuesta_elaborada'] = is_array($dec) ? $dec : null;
        } elseif (!is_array($s['respuesta_elaborada'])) {
            $s['respuesta_elaborada'] = null;
        }
        if (!array_key_exists('respondido_en', $s)) {
            $s['respondido_en'] = '';
        } else {
            $s['respondido_en'] = trim((string) $s['respondido_en']);
        }
        if ($s['respondido_en'] === '') {
            $leg = solicitud_inferir_respondido_en_legacy($s);
            if ($leg !== '') {
                $s['respondido_en'] = $leg;
            }
        }

        return $s;
    }

    /** Cantidad de solicitudes con respuesta/estado nuevo sin revisar en el panel del usuario. */
    public static function conteoNotificacionesParaUsuario(?array $user): int
    {
        if ($user === null) {
            return 0;
        }
        $rol = (string) ($user['rol'] ?? '');
        $id = (int) ($user['id'] ?? 0);
        if ($id <= 0) {
            return 0;
        }
        $rows = load_data('solicitudes');
        $n = 0;
        foreach ($rows as $s) {
            $s = self::normalizarLegacy($s);
            if ($rol === \ROLE_ESTUDIANTE && (int) ($s['id_estudiante'] ?? 0) === $id && !empty($s['notif_pendiente_est'])) {
                $n++;
            }
            if ($rol === \ROLE_DOCENTE && (int) ($s['id_docente_solicitante'] ?? 0) === $id && !empty($s['notif_pendiente_doc'])) {
                $n++;
            }
        }

        return $n;
    }

    /**
     * @return list<array{id_solicitud: int, estado: string, tipo: string, fecha: string}>
     */
    public static function resumenNotificacionesPendientes(?array $user, int $limit = 6): array
    {
        if ($user === null || $limit <= 0) {
            return [];
        }
        $rol = (string) ($user['rol'] ?? '');
        $id = (int) ($user['id'] ?? 0);
        if ($id <= 0) {
            return [];
        }
        $rows = load_data('solicitudes');
        $cand = [];
        foreach ($rows as $s) {
            $s = self::normalizarLegacy($s);
            $match = false;
            if ($rol === \ROLE_ESTUDIANTE && (int) ($s['id_estudiante'] ?? 0) === $id && !empty($s['notif_pendiente_est'])) {
                $match = true;
            }
            if ($rol === \ROLE_DOCENTE && (int) ($s['id_docente_solicitante'] ?? 0) === $id && !empty($s['notif_pendiente_doc'])) {
                $match = true;
            }
            if ($match) {
                $cand[] = $s;
            }
        }
        usort($cand, static fn ($a, $b) => ((int) ($b['id_solicitud'] ?? 0)) <=> ((int) ($a['id_solicitud'] ?? 0)));
        $cand = array_slice($cand, 0, $limit);
        $out = [];
        foreach ($cand as $s) {
            $mom = solicitud_texto_momento_respuesta($s);
            $out[] = [
                'id_solicitud' => (int) ($s['id_solicitud'] ?? 0),
                'estado' => solicitud_estado_nombre((string) ($s['estado'] ?? '')),
                'tipo' => solicitud_tipo_etiqueta($s),
                'fecha' => $mom !== '' ? $mom : (string) ($s['fecha_respuesta'] ?? $s['fecha_registro'] ?? ''),
            ];
        }

        return $out;
    }

    /** Marca como vistas las notificaciones del usuario (p. ej. al abrir «Mis solicitudes»). */
    public static function marcarNotificacionesLeidasParaUsuario(?array $user): void
    {
        if ($user === null) {
            return;
        }
        $rol = (string) ($user['rol'] ?? '');
        $id = (int) ($user['id'] ?? 0);
        if ($id <= 0 || ($rol !== \ROLE_ESTUDIANTE && $rol !== \ROLE_DOCENTE)) {
            return;
        }
        $rows = load_data('solicitudes');
        $changed = false;
        foreach ($rows as &$s) {
            if ($rol === \ROLE_ESTUDIANTE && (int) ($s['id_estudiante'] ?? 0) === $id) {
                if (!empty($s['notif_pendiente_est'])) {
                    $s['notif_pendiente_est'] = false;
                    $changed = true;
                }
            }
            if ($rol === \ROLE_DOCENTE && (int) ($s['id_docente_solicitante'] ?? 0) === $id) {
                if (!empty($s['notif_pendiente_doc'])) {
                    $s['notif_pendiente_doc'] = false;
                    $changed = true;
                }
            }
        }
        unset($s);
        if ($changed) {
            save_data('solicitudes', $rows);
        }
    }
}
