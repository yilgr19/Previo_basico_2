<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Registro y gestión de solicitudes (MySQL/JSON + adjuntos).
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

        $rows = load_data('solicitudes');
        $idSol = next_numeric_id($rows, 'id_solicitud');
        $anexos = [];

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
            'documento_estudiante' => (string) ($est['documento'] ?? ''),
            'id_tipo_solicitud' => $idTipo,
            'codigo_tipo' => (string) ($tipo['codigo'] ?? ''),
            'fecha_registro' => fecha_hora_colombia(),
            'estado' => 'pendiente',
            'descripcion' => $exposicion,
            'respuesta' => '',
            'fecha_respuesta' => '',
            'respondido_en' => '',
            'respuesta_elaborada' => null,
            'anexos_archivos' => $anexos,
            'detalle_estudiante' => $detalleEst,
            'detalle_docente' => null,
            'formulario_version' => 2,
            'notif_pendiente_est' => false,
            'notif_nueva_gestion' => true,
            'docs_pendientes_estudiante' => [],
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
                $respTrim = trim($respuesta);
                $ahora = fecha_hora_colombia();
                $yaRespondida = trim((string) ($s['respondido_en'] ?? '')) !== '';
                $cierraRespuesta = $respTrim !== '' || $guardarRespuestaElaborada;

                $s['estado'] = $nuevoEstado;
                $s['respuesta'] = $respTrim;
                if (!$yaRespondida && $cierraRespuesta) {
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
                $s['notif_nueva_gestion'] = false;
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
            $cod = solicitud_estado_a_codigo((string) ($s['estado'] ?? ''));
            $s['estado'] = $cod;
            if ($est !== '' && $cod !== $est) {
                continue;
            }
            $esAprobada = $cod === 'aprobada';
            $esRechazada = $cod === 'rechazada';
            if ($aprob === 'aprobadas' && !$esAprobada) {
                continue;
            }
            if ($aprob === 'rechazadas' && !$esRechazada) {
                continue;
            }
            if ($aprob === 'no_aprobadas' && $esAprobada) {
                continue;
            }
            if (!empty($f['excluir_cerradas']) && $est === '' && $aprob === '') {
                if ($esAprobada || $esRechazada) {
                    continue;
                }
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
                $docDocenteRad = $docSol ? preg_replace('/\D/', '', (string) ($docSol['documento'] ?? '')) : '';
                $hay = str_contains($docE, $busNorm) || str_contains($docEd, $busNorm)
                    || str_contains($docDocenteRad, $busNorm);
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

    /**
     * Conteos para el panel de gestión: totales y pendiente/en revisión por sede (misma regla que las bandejas).
     *
     * @return array<int, array{total: int, pendiente_revision: int}> claves 1 (Cúcuta) y 2 (Ocaña)
     */
    public static function conteosPanelPorSedeBandeja(): array
    {
        $out = [
            1 => ['total' => 0, 'pendiente_revision' => 0],
            2 => ['total' => 0, 'pendiente_revision' => 0],
        ];
        foreach (load_data('solicitudes') as $row) {
            $s = self::normalizarLegacy($row);
            $idEst = (int) ($s['id_estudiante'] ?? 0);
            $idDocSol = (int) ($s['id_docente_solicitante'] ?? 0);
            $estudiante = $idEst > 0 ? repo_estudiante_por_id($idEst) : null;
            $docSol = $idDocSol > 0 ? repo_docente_por_id($idDocSol) : null;
            $sede = solicitud_sede_para_bandera_gestion($s, $estudiante, $docSol);
            $sedeKey = $sede === 2 ? 2 : 1;

            $out[$sedeKey]['total']++;
            $st = (string) ($s['estado'] ?? '');
            if ($st === 'pendiente' || $st === 'en_revision') {
                $out[$sedeKey]['pendiente_revision']++;
            }
        }

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
        if ($rol === \ROLE_ESTUDIANTE && $idEst > 0 && (int) ($user['id'] ?? 0) === $idEst) {
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
        $s['estado'] = solicitud_estado_a_codigo((string) ($s['estado'] ?? ''));
        if (!array_key_exists('notif_pendiente_est', $s)) {
            $s['notif_pendiente_est'] = false;
        }
        if (!array_key_exists('notif_pendiente_doc', $s)) {
            $s['notif_pendiente_doc'] = false;
        }
        if (!array_key_exists('notif_nueva_gestion', $s)) {
            $s['notif_nueva_gestion'] = false;
        }
        if (!array_key_exists('docs_pendientes_estudiante', $s) || !is_array($s['docs_pendientes_estudiante'])) {
            $s['docs_pendientes_estudiante'] = [];
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

    /**
     * Nuevas solicitudes radicadas sin ver en gestión (panel admin).
     *
     * @return array{estudiantes: int, docentes: int, total: int}
     */
    public static function conteoNotificacionesGestionPorRadicante(): array
    {
        $nEst = 0;
        $nDoc = 0;
        foreach (load_data('solicitudes') as $row) {
            $s = self::normalizarLegacy($row);
            if (empty($s['notif_nueva_gestion'])) {
                continue;
            }
            if ((int) ($s['id_estudiante'] ?? 0) > 0) {
                $nEst++;
            } elseif ((int) ($s['id_docente_solicitante'] ?? 0) > 0) {
                $nDoc++;
            }
        }

        return [
            'estudiantes' => $nEst,
            'docentes' => $nDoc,
            'total' => $nEst + $nDoc,
        ];
    }

    /**
     * @return list<array{
     *   id_solicitud: int,
     *   tipo: string,
     *   fecha: string,
     *   radicante: 'estudiante'|'docente',
     *   semaforo: string,
     *   semaforo_etiqueta: string,
     *   dias_restantes: ?int,
     *   fecha_vencimiento: ?string
     * }>
     */
    public static function resumenNotificacionesGestion(int $limit = 8): array
    {
        if ($limit <= 0) {
            return [];
        }
        $rows = load_data('solicitudes');
        $cand = [];
        foreach ($rows as $s) {
            $s = self::normalizarLegacy($s);
            if (empty($s['notif_nueva_gestion'])) {
                continue;
            }
            $idEst = (int) ($s['id_estudiante'] ?? 0);
            $idDoc = (int) ($s['id_docente_solicitante'] ?? 0);
            if ($idEst > 0) {
                $cand[] = [$s, 'estudiante'];
            } elseif ($idDoc > 0) {
                $cand[] = [$s, 'docente'];
            }
        }

        $prio = ['rojo' => 0, 'amarillo' => 1, 'verde' => 2];
        usort($cand, static function ($a, $b) use ($prio) {
            $semA = PlazosSolicitudService::semaforoSolicitud($a[0]);
            $semB = PlazosSolicitudService::semaforoSolicitud($b[0]);
            $pa = !empty($semA['activo']) ? ($prio[$semA['estado']] ?? 9) : 9;
            $pb = !empty($semB['activo']) ? ($prio[$semB['estado']] ?? 9) : 9;
            if ($pa !== $pb) {
                return $pa <=> $pb;
            }

            return ((int) ($b[0]['id_solicitud'] ?? 0)) <=> ((int) ($a[0]['id_solicitud'] ?? 0));
        });

        $cand = array_slice($cand, 0, $limit);
        $out = [];
        foreach ($cand as [$s, $rad]) {
            $sem = PlazosSolicitudService::semaforoSolicitud($s);
            $out[] = [
                'id_solicitud' => (int) ($s['id_solicitud'] ?? 0),
                'tipo' => solicitud_tipo_etiqueta($s),
                'fecha' => (string) ($s['fecha_registro'] ?? ''),
                'radicante' => $rad,
                'semaforo' => $sem['estado'],
                'semaforo_activo' => !empty($sem['activo']),
                'semaforo_etiqueta' => $sem['etiqueta'],
                'dias_restantes' => $sem['dias_restantes'],
                'fecha_vencimiento' => $sem['fecha_vencimiento'],
            ];
        }

        return $out;
    }

    /** Marca como vistas las notificaciones de nuevas solicitudes (al abrir una bandeja de gestión). */
    public static function marcarNotificacionesGestionLeidas(): void
    {
        $rows = load_data('solicitudes');
        $changed = false;
        foreach ($rows as &$s) {
            if (!empty($s['notif_nueva_gestion'])) {
                $s['notif_nueva_gestion'] = false;
                $changed = true;
            }
        }
        unset($s);
        if ($changed) {
            save_data('solicitudes', $rows);
        }
    }

    /** Marca como vistas las notificaciones del usuario (p. ej. al abrir «Mis solicitudes»). */
    public static function marcarNotificacionesLeidasParaUsuario(?array $user): void
    {
        if ($user === null) {
            return;
        }
        $rol = (string) ($user['rol'] ?? '');
        $id = (int) ($user['id'] ?? 0);
        if ($id <= 0 || $rol !== \ROLE_ESTUDIANTE) {
            return;
        }
        $rows = load_data('solicitudes');
        $changed = false;
        foreach ($rows as &$s) {
            if ($rol === \ROLE_ESTUDIANTE && (int) ($s['id_estudiante'] ?? 0) === $id && !empty($s['notif_pendiente_est'])) {
                $s['notif_pendiente_est'] = false;
                $changed = true;
            }
        }
        unset($s);
        if ($changed) {
            save_data('solicitudes', $rows);
        }
    }
}
