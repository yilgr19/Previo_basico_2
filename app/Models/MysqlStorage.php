<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class MysqlStorage
{
    private const ALLOWED = ['estudiantes', 'docentes', 'administradores', 'solicitudes'];

    public static function load(string $name): array
    {
        if (!in_array($name, self::ALLOWED, true)) {
            return [];
        }

        return match ($name) {
            'estudiantes' => self::loadEstudiantes(),
            'docentes' => self::loadDocentes(),
            'administradores' => self::loadAdministradores(),
            'solicitudes' => self::loadSolicitudes(),
            default => [],
        };
    }

    public static function save(string $name, array $data): bool
    {
        if (!in_array($name, self::ALLOWED, true)) {
            return false;
        }

        $pdo = Database::pdo();

        try {
            $pdo->beginTransaction();
            $ok = match ($name) {
                'estudiantes' => self::saveEstudiantes($pdo, $data),
                'docentes' => self::saveDocentes($pdo, $data),
                'administradores' => self::saveAdministradores($pdo, $data),
                'solicitudes' => self::saveSolicitudes($pdo, $data),
                default => false,
            };
            if ($ok) {
                $pdo->commit();
            } else {
                $pdo->rollBack();
            }

            return $ok;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    private static function loadEstudiantes(): array
    {
        $rows = Database::pdo()->query('SELECT * FROM estudiantes ORDER BY id_estudiante')->fetchAll();
        foreach ($rows as &$r) {
            $r['id_estudiante'] = (int) $r['id_estudiante'];
            $r['id_programa'] = isset($r['id_programa']) ? (int) $r['id_programa'] : 0;
            $r['semestre'] = isset($r['semestre']) ? (int) $r['semestre'] : 0;
            $r['edad'] = isset($r['edad']) ? (int) $r['edad'] : 0;
            $r['id_sede'] = isset($r['id_sede']) ? (int) $r['id_sede'] : 0;
            $r['id_jornada'] = isset($r['id_jornada']) ? (int) $r['id_jornada'] : 0;
        }
        unset($r);

        return $rows;
    }

    private static function loadDocentes(): array
    {
        $rows = Database::pdo()->query('SELECT * FROM docentes ORDER BY id_docente')->fetchAll();
        foreach ($rows as &$r) {
            $r['id_docente'] = (int) $r['id_docente'];
            $r['id_programa'] = isset($r['id_programa']) ? (int) $r['id_programa'] : 0;
            $r['id_sede'] = isset($r['id_sede']) ? (int) $r['id_sede'] : 0;
        }
        unset($r);

        return $rows;
    }

    private static function loadAdministradores(): array
    {
        $rows = Database::pdo()->query('SELECT * FROM administradores ORDER BY id_admin')->fetchAll();
        foreach ($rows as &$r) {
            $r['id_admin'] = (int) $r['id_admin'];
        }
        unset($r);

        return $rows;
    }

    private static function loadSolicitudes(): array
    {
        $pdo = Database::pdo();
        $rows = $pdo->query('SELECT * FROM solicitudes ORDER BY id_solicitud')->fetchAll();
        $out = [];

        $stDe = $pdo->prepare('SELECT * FROM solicitud_detalle_estudiante WHERE id_solicitud = ? LIMIT 1');
        $stDd = $pdo->prepare('SELECT * FROM solicitud_detalle_docente WHERE id_solicitud = ? LIMIT 1');
        $stAx = $pdo->prepare('SELECT guardado, original, mime, bytes, categoria FROM solicitud_anexos WHERE id_solicitud = ? ORDER BY id_anexo');
        $stRe = $pdo->prepare('SELECT * FROM solicitud_respuesta_elaborada WHERE id_solicitud = ? LIMIT 1');

        foreach ($rows as $s) {
            $id = (int) $s['id_solicitud'];
            $s['id_estudiante'] = (int) ($s['id_estudiante'] ?? 0);
            $s['id_docente_solicitante'] = (int) ($s['id_docente_solicitante'] ?? 0);
            $s['id_tipo_solicitud'] = (int) ($s['id_tipo_solicitud'] ?? 0);
            $s['id_tipo_solicitud_docente'] = (int) ($s['id_tipo_solicitud_docente'] ?? 0);
            $s['formulario_version'] = (int) ($s['formulario_version'] ?? 2);
            $s['notif_pendiente_est'] = (bool) ($s['notif_pendiente_est'] ?? false);
            $s['notif_pendiente_doc'] = (bool) ($s['notif_pendiente_doc'] ?? false);
            $s['notif_nueva_gestion'] = (bool) ($s['notif_nueva_gestion'] ?? false);
            $s['fecha_respuesta'] = (string) ($s['fecha_respuesta'] ?? '');
            $s['respondido_en'] = (string) ($s['respondido_en'] ?? '');
            $s['respuesta'] = (string) ($s['respuesta'] ?? '');
            $s['documento_docente_relacionado'] = (string) ($s['documento_docente_relacionado'] ?? '');

            $stDe->execute([$id]);
            $de = $stDe->fetch();
            $s['detalle_estudiante'] = $de ? self::mapDetalleEstudiante($de) : null;

            $stDd->execute([$id]);
            $dd = $stDd->fetch();
            $s['detalle_docente'] = $dd ? self::mapDetalleDocente($dd) : null;

            $stAx->execute([$id]);
            $s['anexos_archivos'] = $stAx->fetchAll();

            $stRe->execute([$id]);
            $re = $stRe->fetch();
            $s['respuesta_elaborada'] = $re ? self::mapRespuestaElaborada($re, $id) : null;

            $out[] = $s;
        }

        return $out;
    }

    /** @param array<string, mixed> $de */
    private static function mapDetalleEstudiante(array $de): array
    {
        return [
            'perfil_snapshot' => [
                'id_estudiantil' => (string) ($de['id_estudiantil'] ?? ''),
                'id_programa' => (int) ($de['id_programa'] ?? 0),
                'programa_nombre' => (string) ($de['programa_nombre'] ?? ''),
                'estado_academico' => (string) ($de['estado_academico'] ?? ''),
                'estado_academico_label' => (string) ($de['estado_academico_label'] ?? ''),
                'semestre' => (int) ($de['semestre'] ?? 0),
                'id_sede_matricula' => (int) ($de['id_sede_matricula'] ?? 0),
                'id_jornada_matricula' => (int) ($de['id_jornada_matricula'] ?? 0),
            ],
            'clasificacion' => [
                'periodo_academico' => (string) ($de['periodo_academico'] ?? ''),
                'id_sede_solicitud' => (int) ($de['id_sede_solicitud'] ?? 0),
                'id_jornada_solicitud' => (int) ($de['id_jornada_solicitud'] ?? 0),
            ],
            'cuerpo' => [
                'motivo' => (string) ($de['motivo'] ?? ''),
                'motivo_label' => (string) ($de['motivo_label'] ?? ''),
                'exposicion' => (string) ($de['exposicion'] ?? ''),
            ],
            'consentimientos' => [
                'veracidad' => (bool) ($de['consentimiento_veracidad'] ?? false),
            ],
        ];
    }

    /** @param array<string, mixed> $dd */
    private static function mapDetalleDocente(array $dd): array
    {
        return [
            'perfil_snapshot' => [
                'id_empleado' => (string) ($dd['id_empleado'] ?? ''),
                'unidad_academica' => (string) ($dd['unidad_academica'] ?? ''),
                'categoria_docente' => (string) ($dd['categoria_docente'] ?? ''),
                'categoria_docente_label' => (string) ($dd['categoria_docente_label'] ?? ''),
                'tipo_contrato' => (string) ($dd['tipo_contrato'] ?? ''),
                'tipo_contrato_label' => (string) ($dd['tipo_contrato_label'] ?? ''),
                'documento' => (string) ($dd['documento'] ?? ''),
                'nombre_completo' => (string) ($dd['nombre_completo'] ?? ''),
            ],
            'clasificacion' => [
                'asunto' => (string) ($dd['asunto'] ?? ''),
                'prioridad' => (string) ($dd['prioridad'] ?? ''),
                'prioridad_label' => (string) ($dd['prioridad_label'] ?? ''),
            ],
            'carga_afectada' => [
                'nrc' => (string) ($dd['nrc'] ?? ''),
                'nombre_materia' => (string) ($dd['nombre_materia'] ?? ''),
                'horario_impactado' => (string) ($dd['horario_impactado'] ?? ''),
                'plan_contingencia' => (string) ($dd['plan_contingencia'] ?? ''),
            ],
            'cuerpo' => [
                'descripcion_detallada' => (string) ($dd['descripcion_detallada'] ?? ''),
                'sustento_legal' => (string) ($dd['sustento_legal'] ?? ''),
                'fecha_inicio' => (string) ($dd['fecha_inicio'] ?? ''),
                'fecha_fin' => (string) ($dd['fecha_fin'] ?? ''),
            ],
            'consentimientos' => [
                'responsabilidad' => (bool) ($dd['consentimiento_responsabilidad'] ?? false),
            ],
        ];
    }

    /** @param array<string, mixed> $re */
    private static function mapRespuestaElaborada(array $re, int $idSolicitud): array
    {
        return [
            'numero_respuesta' => (string) ($re['numero_respuesta'] ?? ''),
            'id_solicitud' => $idSolicitud,
            'emitido_en' => (string) ($re['emitido_en'] ?? ''),
            'decision' => (string) ($re['decision'] ?? ''),
            'justificacion' => (string) ($re['justificacion'] ?? ''),
            'normativas' => (string) ($re['normativas'] ?? ''),
            'subsanacion_items' => (string) ($re['subsanacion_items'] ?? ''),
            'subsanacion_error_doc' => (string) ($re['subsanacion_error_doc'] ?? ''),
            'subsanacion_fecha_limite' => (string) ($re['subsanacion_fecha_limite'] ?? ''),
            'instrucciones_cierre' => (string) ($re['instrucciones_cierre'] ?? ''),
            'recursos_apelacion' => (string) ($re['recursos_apelacion'] ?? ''),
            'funcionario_nombre' => (string) ($re['funcionario_nombre'] ?? ''),
            'funcionario_cargo' => (string) ($re['funcionario_cargo'] ?? ''),
            'codigo_verificacion' => (string) ($re['codigo_verificacion'] ?? ''),
        ];
    }

    private static function saveEstudiantes(PDO $pdo, array $data): bool
    {
        $sql = 'INSERT INTO estudiantes (
            id_estudiante, tipo_identificacion, documento, nombre, apellido, correo, sexo,
            id_programa, programa, estado_academico, semestre, fecha_nacimiento, edad,
            direccion, barrio, telefono, id_sede, id_jornada, clave
        ) VALUES (
            :id_estudiante, :tipo_identificacion, :documento, :nombre, :apellido, :correo, :sexo,
            :id_programa, :programa, :estado_academico, :semestre, :fecha_nacimiento, :edad,
            :direccion, :barrio, :telefono, :id_sede, :id_jornada, :clave
        ) ON DUPLICATE KEY UPDATE
            tipo_identificacion=VALUES(tipo_identificacion), documento=VALUES(documento),
            nombre=VALUES(nombre), apellido=VALUES(apellido), correo=VALUES(correo), sexo=VALUES(sexo),
            id_programa=VALUES(id_programa), programa=VALUES(programa), estado_academico=VALUES(estado_academico),
            semestre=VALUES(semestre), fecha_nacimiento=VALUES(fecha_nacimiento), edad=VALUES(edad),
            direccion=VALUES(direccion), barrio=VALUES(barrio), telefono=VALUES(telefono),
            id_sede=VALUES(id_sede), id_jornada=VALUES(id_jornada), clave=VALUES(clave)';

        $st = $pdo->prepare($sql);
        foreach ($data as $r) {
            $st->execute([
                'id_estudiante' => (int) ($r['id_estudiante'] ?? 0),
                'tipo_identificacion' => (string) ($r['tipo_identificacion'] ?? 'CC'),
                'documento' => (string) ($r['documento'] ?? ''),
                'nombre' => (string) ($r['nombre'] ?? ''),
                'apellido' => (string) ($r['apellido'] ?? ''),
                'correo' => (string) ($r['correo'] ?? ''),
                'sexo' => self::nullIfEmpty($r['sexo'] ?? null),
                'id_programa' => self::nullIfZeroInt($r['id_programa'] ?? 0),
                'programa' => self::nullIfEmpty($r['programa'] ?? null),
                'estado_academico' => (string) ($r['estado_academico'] ?? 'REGULAR'),
                'semestre' => self::nullIfZeroInt($r['semestre'] ?? 0),
                'fecha_nacimiento' => self::nullIfEmpty($r['fecha_nacimiento'] ?? null),
                'edad' => self::nullIfZeroInt($r['edad'] ?? 0),
                'direccion' => self::nullIfEmpty($r['direccion'] ?? null),
                'barrio' => self::nullIfEmpty($r['barrio'] ?? null),
                'telefono' => self::nullIfEmpty($r['telefono'] ?? null),
                'id_sede' => self::nullIfZeroInt($r['id_sede'] ?? 0),
                'id_jornada' => self::nullIfZeroInt($r['id_jornada'] ?? 0),
                'clave' => (string) ($r['clave'] ?? ''),
            ]);
        }

        return true;
    }

    private static function saveDocentes(PDO $pdo, array $data): bool
    {
        $sql = 'INSERT INTO docentes (
            id_docente, nombre, apellido, documento, correo, telefono, id_sede, id_programa,
            programa, codigo_empleado, unidad_academica, categoria_docente, tipo_contrato, clave
        ) VALUES (
            :id_docente, :nombre, :apellido, :documento, :correo, :telefono, :id_sede, :id_programa,
            :programa, :codigo_empleado, :unidad_academica, :categoria_docente, :tipo_contrato, :clave
        ) ON DUPLICATE KEY UPDATE
            nombre=VALUES(nombre), apellido=VALUES(apellido), documento=VALUES(documento),
            correo=VALUES(correo), telefono=VALUES(telefono), id_sede=VALUES(id_sede),
            id_programa=VALUES(id_programa), programa=VALUES(programa), codigo_empleado=VALUES(codigo_empleado),
            unidad_academica=VALUES(unidad_academica), categoria_docente=VALUES(categoria_docente),
            tipo_contrato=VALUES(tipo_contrato), clave=VALUES(clave)';

        $st = $pdo->prepare($sql);
        foreach ($data as $r) {
            $st->execute([
                'id_docente' => (int) ($r['id_docente'] ?? 0),
                'nombre' => (string) ($r['nombre'] ?? ''),
                'apellido' => (string) ($r['apellido'] ?? ''),
                'documento' => (string) ($r['documento'] ?? ''),
                'correo' => (string) ($r['correo'] ?? ''),
                'telefono' => self::nullIfEmpty($r['telefono'] ?? null),
                'id_sede' => self::nullIfZeroInt($r['id_sede'] ?? 0),
                'id_programa' => self::nullIfZeroInt($r['id_programa'] ?? 0),
                'programa' => self::nullIfEmpty($r['programa'] ?? null),
                'codigo_empleado' => self::nullIfEmpty($r['codigo_empleado'] ?? null),
                'unidad_academica' => self::nullIfEmpty($r['unidad_academica'] ?? null),
                'categoria_docente' => self::nullIfEmpty($r['categoria_docente'] ?? null),
                'tipo_contrato' => self::nullIfEmpty($r['tipo_contrato'] ?? null),
                'clave' => (string) ($r['clave'] ?? ''),
            ]);
        }

        return true;
    }

    private static function saveAdministradores(PDO $pdo, array $data): bool
    {
        $sql = 'INSERT INTO administradores (id_admin, nombre, correo, rol, clave)
            VALUES (:id_admin, :nombre, :correo, :rol, :clave)
            ON DUPLICATE KEY UPDATE nombre=VALUES(nombre), correo=VALUES(correo), rol=VALUES(rol), clave=VALUES(clave)';

        $st = $pdo->prepare($sql);
        foreach ($data as $r) {
            $st->execute([
                'id_admin' => (int) ($r['id_admin'] ?? 0),
                'nombre' => (string) ($r['nombre'] ?? ''),
                'correo' => (string) ($r['correo'] ?? ''),
                'rol' => (string) ($r['rol'] ?? 'Administrador'),
                'clave' => (string) ($r['clave'] ?? ''),
            ]);
        }

        return true;
    }

    private static function saveSolicitudes(PDO $pdo, array $data): bool
    {
        $ids = [];
        foreach ($data as $s) {
            $id = (int) ($s['id_solicitud'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            $ids[] = $id;
            self::upsertSolicitud($pdo, $s);
        }

        if ($ids !== []) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $pdo->prepare('DELETE FROM solicitudes WHERE id_solicitud NOT IN (' . $placeholders . ')')
                ->execute($ids);
        } else {
            $pdo->exec('DELETE FROM solicitudes');
        }

        return true;
    }

    /** @param array<string, mixed> $s */
    private static function upsertSolicitud(PDO $pdo, array $s): void
    {
        $id = (int) ($s['id_solicitud'] ?? 0);
        $fr = self::normalizarDateTime((string) ($s['fecha_registro'] ?? date('Y-m-d H:i:s')));

        $sql = 'INSERT INTO solicitudes (
            id_solicitud, id_estudiante, id_docente_solicitante, documento_estudiante,
            id_tipo_solicitud, id_tipo_solicitud_docente, codigo_tipo, fecha_registro, estado,
            descripcion, documento_docente_relacionado, respuesta, fecha_respuesta, respondido_en,
            formulario_version, notif_pendiente_est, notif_pendiente_doc, notif_nueva_gestion
        ) VALUES (
            :id_solicitud, :id_estudiante, :id_docente_solicitante, :documento_estudiante,
            :id_tipo_solicitud, :id_tipo_solicitud_docente, :codigo_tipo, :fecha_registro, :estado,
            :descripcion, :documento_docente_relacionado, :respuesta, :fecha_respuesta, :respondido_en,
            :formulario_version, :notif_pendiente_est, :notif_pendiente_doc, :notif_nueva_gestion
        ) ON DUPLICATE KEY UPDATE
            id_estudiante=VALUES(id_estudiante), id_docente_solicitante=VALUES(id_docente_solicitante),
            documento_estudiante=VALUES(documento_estudiante), id_tipo_solicitud=VALUES(id_tipo_solicitud),
            id_tipo_solicitud_docente=VALUES(id_tipo_solicitud_docente), codigo_tipo=VALUES(codigo_tipo),
            fecha_registro=VALUES(fecha_registro), estado=VALUES(estado), descripcion=VALUES(descripcion),
            documento_docente_relacionado=VALUES(documento_docente_relacionado), respuesta=VALUES(respuesta),
            fecha_respuesta=VALUES(fecha_respuesta), respondido_en=VALUES(respondido_en),
            formulario_version=VALUES(formulario_version), notif_pendiente_est=VALUES(notif_pendiente_est),
            notif_pendiente_doc=VALUES(notif_pendiente_doc), notif_nueva_gestion=VALUES(notif_nueva_gestion)';

        $respondido = trim((string) ($s['respondido_en'] ?? ''));
        $fechaResp = self::nullIfEmpty($s['fecha_respuesta'] ?? null);

        $pdo->prepare($sql)->execute([
            'id_solicitud' => $id,
            'id_estudiante' => self::nullIfZeroInt($s['id_estudiante'] ?? 0),
            'id_docente_solicitante' => self::nullIfZeroInt($s['id_docente_solicitante'] ?? 0),
            'documento_estudiante' => self::strOrEmpty($s['documento_estudiante'] ?? ''),
            'id_tipo_solicitud' => self::nullIfZeroInt($s['id_tipo_solicitud'] ?? 0),
            'id_tipo_solicitud_docente' => self::nullIfZeroInt($s['id_tipo_solicitud_docente'] ?? 0),
            'codigo_tipo' => self::strOrEmpty($s['codigo_tipo'] ?? ''),
            'fecha_registro' => $fr,
            'estado' => solicitud_estado_a_codigo((string) ($s['estado'] ?? 'pendiente')),
            'descripcion' => self::strOrEmpty($s['descripcion'] ?? ''),
            'documento_docente_relacionado' => self::strOrEmpty($s['documento_docente_relacionado'] ?? ''),
            'respuesta' => self::strOrEmpty($s['respuesta'] ?? ''),
            'fecha_respuesta' => $fechaResp !== null && $fechaResp !== '' ? $fechaResp : null,
            'respondido_en' => $respondido !== '' ? self::normalizarDateTime($respondido) : null,
            'formulario_version' => (int) ($s['formulario_version'] ?? 2),
            'notif_pendiente_est' => !empty($s['notif_pendiente_est']) ? 1 : 0,
            'notif_pendiente_doc' => !empty($s['notif_pendiente_doc']) ? 1 : 0,
            'notif_nueva_gestion' => !empty($s['notif_nueva_gestion']) ? 1 : 0,
        ]);

        $pdo->prepare('DELETE FROM solicitud_detalle_estudiante WHERE id_solicitud = ?')->execute([$id]);
        $pdo->prepare('DELETE FROM solicitud_detalle_docente WHERE id_solicitud = ?')->execute([$id]);
        $pdo->prepare('DELETE FROM solicitud_anexos WHERE id_solicitud = ?')->execute([$id]);
        $pdo->prepare('DELETE FROM solicitud_respuesta_elaborada WHERE id_solicitud = ?')->execute([$id]);

        $de = $s['detalle_estudiante'] ?? null;
        if (is_array($de)) {
            self::insertDetalleEstudiante($pdo, $id, $de);
        }

        $dd = $s['detalle_docente'] ?? null;
        if (is_array($dd)) {
            self::insertDetalleDocente($pdo, $id, $dd);
        }

        $anexos = $s['anexos_archivos'] ?? [];
        if (is_array($anexos)) {
            self::insertAnexos($pdo, $id, $anexos);
        }

        $re = $s['respuesta_elaborada'] ?? null;
        if (is_array($re) && trim((string) ($re['numero_respuesta'] ?? '')) !== '') {
            self::insertRespuestaElaborada($pdo, $id, $re);
        }
    }

    /** @param array<string, mixed> $de */
    private static function insertDetalleEstudiante(PDO $pdo, int $id, array $de): void
    {
        $ps = $de['perfil_snapshot'] ?? [];
        $cl = $de['clasificacion'] ?? [];
        $cu = $de['cuerpo'] ?? [];
        $co = $de['consentimientos'] ?? [];

        $motivo = self::strOrEmpty($cu['motivo'] ?? '');

        $pdo->prepare(
            'INSERT INTO solicitud_detalle_estudiante (
                id_solicitud, id_estudiantil, id_programa, programa_nombre, estado_academico,
                estado_academico_label, semestre, id_sede_matricula, id_jornada_matricula,
                periodo_academico, id_sede_solicitud, id_jornada_solicitud, motivo, motivo_label,
                exposicion, consentimiento_veracidad
            ) VALUES (
                :id_solicitud, :id_estudiantil, :id_programa, :programa_nombre, :estado_academico,
                :estado_academico_label, :semestre, :id_sede_matricula, :id_jornada_matricula,
                :periodo_academico, :id_sede_solicitud, :id_jornada_solicitud, :motivo, :motivo_label,
                :exposicion, :consentimiento_veracidad
            )'
        )->execute([
            'id_solicitud' => $id,
            'id_estudiantil' => self::strOrEmpty($ps['id_estudiantil'] ?? ''),
            'id_programa' => self::nullIfZeroInt($ps['id_programa'] ?? 0),
            'programa_nombre' => self::strOrEmpty($ps['programa_nombre'] ?? ''),
            'estado_academico' => self::strOrEmpty($ps['estado_academico'] ?? ''),
            'estado_academico_label' => self::strOrEmpty($ps['estado_academico_label'] ?? ''),
            'semestre' => self::nullIfZeroInt($ps['semestre'] ?? 0),
            'id_sede_matricula' => self::nullIfZeroInt($ps['id_sede_matricula'] ?? 0),
            'id_jornada_matricula' => self::nullIfZeroInt($ps['id_jornada_matricula'] ?? 0),
            'periodo_academico' => self::strOrEmpty($cl['periodo_academico'] ?? ''),
            'id_sede_solicitud' => self::nullIfZeroInt($cl['id_sede_solicitud'] ?? 0),
            'id_jornada_solicitud' => self::nullIfZeroInt($cl['id_jornada_solicitud'] ?? 0),
            'motivo' => $motivo ?? '',
            'motivo_label' => self::strOrEmpty($cu['motivo_label'] ?? ''),
            'exposicion' => self::strOrEmpty($cu['exposicion'] ?? ''),
            'consentimiento_veracidad' => !empty($co['veracidad']) ? 1 : 0,
        ]);
    }

    /** @param array<string, mixed> $dd */
    private static function insertDetalleDocente(PDO $pdo, int $id, array $dd): void
    {
        $ps = $dd['perfil_snapshot'] ?? [];
        $cl = $dd['clasificacion'] ?? [];
        $ca = $dd['carga_afectada'] ?? [];
        $cu = $dd['cuerpo'] ?? [];
        $co = $dd['consentimientos'] ?? [];

        $pdo->prepare(
            'INSERT INTO solicitud_detalle_docente (
                id_solicitud, id_empleado, unidad_academica, categoria_docente, categoria_docente_label,
                tipo_contrato, tipo_contrato_label, documento, nombre_completo, asunto, prioridad,
                prioridad_label, nrc, nombre_materia, horario_impactado, plan_contingencia,
                descripcion_detallada, sustento_legal, fecha_inicio, fecha_fin, consentimiento_responsabilidad
            ) VALUES (
                :id_solicitud, :id_empleado, :unidad_academica, :categoria_docente, :categoria_docente_label,
                :tipo_contrato, :tipo_contrato_label, :documento, :nombre_completo, :asunto, :prioridad,
                :prioridad_label, :nrc, :nombre_materia, :horario_impactado, :plan_contingencia,
                :descripcion_detallada, :sustento_legal, :fecha_inicio, :fecha_fin, :consentimiento_responsabilidad
            )'
        )->execute([
            'id_solicitud' => $id,
            'id_empleado' => self::strOrEmpty($ps['id_empleado'] ?? ''),
            'unidad_academica' => self::strOrEmpty($ps['unidad_academica'] ?? ''),
            'categoria_docente' => self::strOrEmpty($ps['categoria_docente'] ?? ''),
            'categoria_docente_label' => self::strOrEmpty($ps['categoria_docente_label'] ?? ''),
            'tipo_contrato' => self::strOrEmpty($ps['tipo_contrato'] ?? ''),
            'tipo_contrato_label' => self::strOrEmpty($ps['tipo_contrato_label'] ?? ''),
            'documento' => self::strOrEmpty($ps['documento'] ?? ''),
            'nombre_completo' => self::strOrEmpty($ps['nombre_completo'] ?? ''),
            'asunto' => self::strOrEmpty($cl['asunto'] ?? ''),
            'prioridad' => self::strOrEmpty($cl['prioridad'] ?? ''),
            'prioridad_label' => self::strOrEmpty($cl['prioridad_label'] ?? ''),
            'nrc' => self::strOrEmpty($ca['nrc'] ?? ''),
            'nombre_materia' => self::strOrEmpty($ca['nombre_materia'] ?? ''),
            'horario_impactado' => self::strOrEmpty($ca['horario_impactado'] ?? ''),
            'plan_contingencia' => self::strOrEmpty($ca['plan_contingencia'] ?? ''),
            'descripcion_detallada' => self::strOrEmpty($cu['descripcion_detallada'] ?? ''),
            'sustento_legal' => self::strOrEmpty($cu['sustento_legal'] ?? ''),
            'fecha_inicio' => self::strOrEmpty($cu['fecha_inicio'] ?? ''),
            'fecha_fin' => self::strOrEmpty($cu['fecha_fin'] ?? ''),
            'consentimiento_responsabilidad' => !empty($co['responsabilidad']) ? 1 : 0,
        ]);
    }

    /** @param list<array<string, mixed>> $anexos */
    private static function insertAnexos(PDO $pdo, int $id, array $anexos): void
    {
        $st = $pdo->prepare(
            'INSERT INTO solicitud_anexos (id_solicitud, guardado, original, mime, bytes, categoria)
             VALUES (:id_solicitud, :guardado, :original, :mime, :bytes, :categoria)'
        );
        foreach ($anexos as $a) {
            if (!is_array($a) || trim((string) ($a['guardado'] ?? '')) === '') {
                continue;
            }
            $st->execute([
                'id_solicitud' => $id,
                'guardado' => (string) $a['guardado'],
                'original' => (string) ($a['original'] ?? 'archivo'),
                'mime' => self::nullIfEmpty($a['mime'] ?? null),
                'bytes' => isset($a['bytes']) && (int) $a['bytes'] > 0 ? (int) $a['bytes'] : null,
                'categoria' => (string) ($a['categoria'] ?? 'general'),
            ]);
        }
    }

    /** @param array<string, mixed> $re */
    private static function insertRespuestaElaborada(PDO $pdo, int $id, array $re): void
    {
        $emit = self::normalizarDateTime((string) ($re['emitido_en'] ?? date('Y-m-d H:i:s')));

        $pdo->prepare(
            'INSERT INTO solicitud_respuesta_elaborada (
                id_solicitud, numero_respuesta, emitido_en, decision, justificacion, normativas,
                subsanacion_items, subsanacion_error_doc, subsanacion_fecha_limite,
                instrucciones_cierre, recursos_apelacion, funcionario_nombre, funcionario_cargo,
                codigo_verificacion
            ) VALUES (
                :id_solicitud, :numero_respuesta, :emitido_en, :decision, :justificacion, :normativas,
                :subsanacion_items, :subsanacion_error_doc, :subsanacion_fecha_limite,
                :instrucciones_cierre, :recursos_apelacion, :funcionario_nombre, :funcionario_cargo,
                :codigo_verificacion
            )'
        )->execute([
            'id_solicitud' => $id,
            'numero_respuesta' => (string) ($re['numero_respuesta'] ?? ('RES-' . $id)),
            'emitido_en' => $emit,
            'decision' => (string) ($re['decision'] ?? 'pendiente_informacion'),
            'justificacion' => self::nullIfEmpty($re['justificacion'] ?? null),
            'normativas' => self::nullIfEmpty($re['normativas'] ?? null),
            'subsanacion_items' => self::nullIfEmpty($re['subsanacion_items'] ?? null),
            'subsanacion_error_doc' => self::nullIfEmpty($re['subsanacion_error_doc'] ?? null),
            'subsanacion_fecha_limite' => self::nullIfEmpty($re['subsanacion_fecha_limite'] ?? null),
            'instrucciones_cierre' => self::nullIfEmpty($re['instrucciones_cierre'] ?? null),
            'recursos_apelacion' => self::nullIfEmpty($re['recursos_apelacion'] ?? null),
            'funcionario_nombre' => self::nullIfEmpty($re['funcionario_nombre'] ?? null),
            'funcionario_cargo' => self::nullIfEmpty($re['funcionario_cargo'] ?? null),
            'codigo_verificacion' => self::nullIfEmpty($re['codigo_verificacion'] ?? null),
        ]);
    }

    private static function nullIfEmpty(mixed $v): ?string
    {
        if ($v === null) {
            return null;
        }
        $s = trim((string) $v);

        return $s === '' ? null : $s;
    }

    /** Texto vacío en lugar de NULL (phpMyAdmin y reportes más legibles). */
    private static function strOrEmpty(mixed $v): string
    {
        if ($v === null) {
            return '';
        }

        return trim((string) $v);
    }

    private static function nullIfZeroInt(mixed $v): ?int
    {
        $n = (int) $v;

        return $n > 0 ? $n : null;
    }

    private static function normalizarDateTime(string $v): string
    {
        $v = trim($v);
        if ($v === '') {
            return date('Y-m-d H:i:s');
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
            return $v . ' 00:00:00';
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $v)) {
            return $v;
        }

        return date('Y-m-d H:i:s');
    }

    /** @return array<int, array{id: int, codigo: string, nombre: string, id_sede: int}> */
    public static function catalogProgramas(): array
    {
        $rows = Database::pdo()->query(
            'SELECT id_programa AS id, codigo, nombre, id_sede FROM programas ORDER BY id_programa'
        )->fetchAll();
        foreach ($rows as &$r) {
            $r['id'] = (int) $r['id'];
            $r['id_sede'] = (int) $r['id_sede'];
        }
        unset($r);

        return $rows;
    }

    /** @return array<int, array{id: int, nombre: string}> */
    public static function catalogSedes(): array
    {
        $rows = Database::pdo()->query(
            'SELECT id_sede AS id, nombre FROM sedes ORDER BY id_sede'
        )->fetchAll();
        foreach ($rows as &$r) {
            $r['id'] = (int) $r['id'];
        }
        unset($r);

        return $rows;
    }

    /** @return array<int, array{id: int, nombre: string}> */
    public static function catalogJornadas(): array
    {
        $rows = Database::pdo()->query(
            'SELECT id_jornada AS id, nombre FROM jornadas ORDER BY id_jornada'
        )->fetchAll();
        foreach ($rows as &$r) {
            $r['id'] = (int) $r['id'];
        }
        unset($r);

        return $rows;
    }

    /** @return array<int, array{codigo: string, nombre: string}> */
    public static function catalogTiposIdentificacion(): array
    {
        return Database::pdo()->query(
            'SELECT codigo, nombre FROM tipos_identificacion ORDER BY codigo'
        )->fetchAll();
    }

    /** @return array<int, array{codigo: string, nombre: string}> */
    public static function catalogSexos(): array
    {
        return Database::pdo()->query(
            'SELECT codigo, nombre FROM sexos ORDER BY codigo'
        )->fetchAll();
    }

    /** @return array<int, array{id: int, codigo: string, nombre: string}> */
    public static function catalogTiposSolicitudEstudiante(): array
    {
        $rows = Database::pdo()->query(
            'SELECT id_tipo_solicitud AS id, codigo, nombre FROM tipos_solicitud_estudiante ORDER BY id_tipo_solicitud'
        )->fetchAll();
        foreach ($rows as &$r) {
            $r['id'] = (int) $r['id'];
        }
        unset($r);

        return $rows;
    }

    /** @return array<int, array{codigo: string, nombre: string, aprobada: bool}> */
    public static function catalogEstadosSolicitud(): array
    {
        $rows = Database::pdo()->query(
            'SELECT codigo, nombre, aprobada FROM estados_solicitud ORDER BY codigo'
        )->fetchAll();
        foreach ($rows as &$r) {
            $r['aprobada'] = (bool) ($r['aprobada'] ?? false);
        }
        unset($r);

        return $rows;
    }

    /** @return array<int, array{codigo: string, nombre: string}> */
    public static function catalogMotivosSolicitudEstudiante(): array
    {
        return Database::pdo()->query(
            'SELECT codigo, nombre FROM motivos_solicitud_estudiante ORDER BY codigo'
        )->fetchAll();
    }

    /** @return array<int, array{id: int, codigo: string, nombre: string}> */
    public static function catalogTiposSolicitudDocente(): array
    {
        $rows = Database::pdo()->query(
            'SELECT id_tipo_solicitud_docente AS id, codigo, nombre FROM tipos_solicitud_docente ORDER BY id_tipo_solicitud_docente'
        )->fetchAll();
        foreach ($rows as &$r) {
            $r['id'] = (int) $r['id'];
        }
        unset($r);

        return $rows;
    }

    /** @return array<int, array{codigo: string, nombre: string}> */
    public static function catalogPrioridadesSolicitudDocente(): array
    {
        return Database::pdo()->query(
            'SELECT codigo, nombre FROM prioridades_solicitud_docente ORDER BY codigo'
        )->fetchAll();
    }

    /** @return array<int, array{codigo: string, nombre: string}> */
    public static function catalogCategoriasDocente(): array
    {
        return Database::pdo()->query(
            'SELECT codigo, nombre FROM categorias_docente ORDER BY codigo'
        )->fetchAll();
    }

    /** @return array<int, array{codigo: string, nombre: string}> */
    public static function catalogTiposContratoDocente(): array
    {
        return Database::pdo()->query(
            'SELECT codigo, nombre FROM tipos_contrato_docente ORDER BY codigo'
        )->fetchAll();
    }

    /** @return array<int, array{codigo: string, nombre: string}> */
    public static function catalogDecisionesResolucionFormal(): array
    {
        return Database::pdo()->query(
            'SELECT codigo, nombre FROM decisiones_resolucion_formal ORDER BY codigo'
        )->fetchAll();
    }
}
