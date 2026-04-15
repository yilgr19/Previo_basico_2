<?php
declare(strict_types=1);

function diccionario_programas(): array
{
    return [
        ['id' => 117, 'codigo' => '90604', 'nombre' => 'Técnica Profesional en Operaciones Logísticas', 'id_sede' => 1],
        ['id' => 118, 'codigo' => '90605', 'nombre' => 'Tecnología en Gestión Logística Empresarial', 'id_sede' => 1],
        ['id' => 119, 'codigo' => '91390', 'nombre' => 'Técnica Profesional en Producción Gráfica', 'id_sede' => 1],
        ['id' => 120, 'codigo' => '107156', 'nombre' => 'Tecn. en Gestión de Contenidos Gráficos Publicitarios', 'id_sede' => 1],
        ['id' => 121, 'codigo' => '91388', 'nombre' => 'Diseño Gráfico', 'id_sede' => 1],
        ['id' => 123, 'codigo' => '107859', 'nombre' => 'Técnica Profesional en Soporte Informático', 'id_sede' => 1],
        ['id' => 124, 'codigo' => '107860', 'nombre' => 'Tecnología en Desarrollo de Software', 'id_sede' => 1],
        ['id' => 125, 'codigo' => '107861', 'nombre' => 'Ingeniería de Software', 'id_sede' => 1],
        ['id' => 126, 'codigo' => '107858', 'nombre' => 'Especialización en Gestión Pública', 'id_sede' => 1],
        ['id' => 127, 'codigo' => '108788', 'nombre' => 'Tecn. en Gestión de Contenidos Gráficos Public. Ocaña', 'id_sede' => 2],
        ['id' => 128, 'codigo' => '102041', 'nombre' => 'Diseño Gráfico Ocaña', 'id_sede' => 2],
        ['id' => 130, 'codigo' => '102517', 'nombre' => 'Tecn. en Gestión de Negocios Internacionales Ocaña', 'id_sede' => 2],
        ['id' => 131, 'codigo' => '102518', 'nombre' => 'Administración de Negocios Internacionales Ocaña', 'id_sede' => 2],
        ['id' => 133, 'codigo' => '102887', 'nombre' => 'Técnica Prof. en Operaciones Turísticas Virtual', 'id_sede' => 1],
        ['id' => 134, 'codigo' => '111410', 'nombre' => 'Tecnología en Gestión del Turismo Sostenible Virtual', 'id_sede' => 1],
        ['id' => 137, 'codigo' => '111412', 'nombre' => 'Tecnología en Gestión del Turismo Sostenible Presencial', 'id_sede' => 1],
        ['id' => 143, 'codigo' => '54348', 'nombre' => 'Técnica Profesional en Procesos Contables Presencial', 'id_sede' => 1],
        ['id' => 153, 'codigo' => '116880', 'nombre' => 'Administración de Negocios Internacionales Presencial', 'id_sede' => 1],
        ['id' => 159, 'codigo' => '104671', 'nombre' => 'Profesional en Diseño y Administración de Negocios de la Moda', 'id_sede' => 1],
        ['id' => 166, 'codigo' => '117775', 'nombre' => 'Especialización en Analítica de Datos para los Negocios Virtual', 'id_sede' => 1],
        ['id' => 176, 'codigo' => '91388', 'nombre' => 'Profesional en Diseño Gráfico', 'id_sede' => 1],
        ['id' => 177, 'codigo' => '118275', 'nombre' => 'Especialización en Marketing Digital Estratégico Presencial', 'id_sede' => 1],
    ];
}

function diccionario_sedes(): array
{
    return [
        ['id' => 1, 'nombre' => 'Cúcuta'],
        ['id' => 2, 'nombre' => 'Ocaña'],
    ];
}

function diccionario_jornadas(): array
{
    return [
        ['id' => 1, 'nombre' => 'Diurna'],
        ['id' => 2, 'nombre' => 'Nocturna'],
        ['id' => 3, 'nombre' => 'Distancia'],
        ['id' => 4, 'nombre' => 'Virtual'],
    ];
}

function diccionario_tipos_identificacion(): array
{
    return [
        ['codigo' => 'CC', 'nombre' => 'Cédula de ciudadanía'],
        ['codigo' => 'TI', 'nombre' => 'Tarjeta de identidad'],
        ['codigo' => 'CE', 'nombre' => 'Cédula de extranjería'],
        ['codigo' => 'PPT', 'nombre' => 'Permiso por protección temporal'],
        ['codigo' => 'PAS', 'nombre' => 'Pasaporte'],
    ];
}

function diccionario_sexo(): array
{
    return [
        ['codigo' => 'M', 'nombre' => 'Masculino'],
        ['codigo' => 'F', 'nombre' => 'Femenino'],
        ['codigo' => 'O', 'nombre' => 'Otro / Prefiero no indicar'],
    ];
}

function tipo_identificacion_nombre(?string $cod): string
{
    if ($cod === null || $cod === '') {
        return '';
    }
    foreach (diccionario_tipos_identificacion() as $t) {
        if (($t['codigo'] ?? '') === $cod) {
            return $t['nombre'];
        }
    }
    return $cod;
}

function sexo_nombre(?string $cod): string
{
    if ($cod === null || $cod === '') {
        return '';
    }
    foreach (diccionario_sexo() as $t) {
        if (($t['codigo'] ?? '') === $cod) {
            return $t['nombre'];
        }
    }
    return $cod;
}

function programa_label_by_id(int $id): string
{
    foreach (diccionario_programas() as $p) {
        if ((int) $p['id'] === $id) {
            return '[' . $p['codigo'] . '] ' . $p['nombre'];
        }
    }
    return 'Programa ID ' . $id;
}

function programa_id_sede(int $idPrograma): int
{
    foreach (diccionario_programas() as $p) {
        if ((int) $p['id'] === $idPrograma) {
            return (int) ($p['id_sede'] ?? 1);
        }
    }
    return 1;
}

function docente_sede_efectiva(array $docente): int
{
    if (isset($docente['id_sede']) && (int) $docente['id_sede'] > 0) {
        return (int) $docente['id_sede'];
    }
    if (!empty($docente['id_programa'])) {
        return programa_id_sede((int) $docente['id_programa']);
    }
    return 1;
}

function sede_nombre(?int $id): string
{
    if ($id === null) {
        return '';
    }
    foreach (diccionario_sedes() as $s) {
        if ((int) $s['id'] === $id) {
            return $s['nombre'];
        }
    }
    return '';
}

function jornada_nombre(?int $id): string
{
    if ($id === null) {
        return '';
    }
    foreach (diccionario_jornadas() as $j) {
        if ((int) $j['id'] === $id) {
            return $j['nombre'];
        }
    }
    return '';
}

/**
 * Tipos de solicitud (catálogo institucional — 13 tipos).
 *
 * @return array<int, array{id: int, codigo: string, nombre: string}>
 */
function diccionario_tipos_solicitud(): array
{
    return [
        ['id' => 1, 'codigo' => 'REQ_CANCEL_SEM', 'nombre' => 'Cancelación de semestre'],
        ['id' => 2, 'codigo' => 'REQ_CURSO_DIR', 'nombre' => 'Curso dirigido'],
        ['id' => 3, 'codigo' => 'REQ_CANCEL_ASIG', 'nombre' => 'Cancelación de asignaturas'],
        ['id' => 4, 'codigo' => 'REQ_CAMBIO_JORNADA', 'nombre' => 'Cambio de jornada'],
        ['id' => 5, 'codigo' => 'REQ_TRANSFER_INT', 'nombre' => 'Transferencia interna'],
        ['id' => 6, 'codigo' => 'REQ_EXAMEN_SUF', 'nombre' => 'Examen de validación por suficiencia'],
        ['id' => 7, 'codigo' => 'REQ_REINGRESO', 'nombre' => 'Reingreso'],
        ['id' => 8, 'codigo' => 'REQ_MATR_MIN', 'nombre' => 'Matrícula mínima de créditos'],
        ['id' => 9, 'codigo' => 'REQ_TRASLADO_SEDE', 'nombre' => 'Traslado de sede'],
        ['id' => 10, 'codigo' => 'REQ_PAGO_CRED', 'nombre' => 'Pago de créditos adicionales'],
        ['id' => 11, 'codigo' => 'REQ_CONST_EST', 'nombre' => 'Constancia de estudio'],
        ['id' => 12, 'codigo' => 'REQ_CERT_NOTAS', 'nombre' => 'Certificado de notas'],
        ['id' => 13, 'codigo' => 'REQ_OTRA', 'nombre' => 'Otra'],
    ];
}

/** Referencia opaca para vistas donde no se revela el radicante (p. ej. docente mencionado). */
function solicitud_referencia_anonima(int $idSolicitud): string
{
    return 'CASO-' . strtoupper(substr(sha1('anon|' . $idSolicitud . '|solicitud'), 0, 10));
}

function tipo_solicitud_por_id(int $id): ?array
{
    foreach (diccionario_tipos_solicitud() as $t) {
        if ((int) ($t['id'] ?? 0) === $id) {
            return $t;
        }
    }
    return null;
}

function tipo_solicitud_nombre(int $id): string
{
    $t = tipo_solicitud_por_id($id);
    return $t ? (string) $t['nombre'] : '—';
}

/**
 * Estados del flujo de solicitud.
 *
 * @return array<int, array{codigo: string, nombre: string, aprobada: bool}>
 */
function diccionario_estados_solicitud(): array
{
    return [
        ['codigo' => 'pendiente', 'nombre' => 'Pendiente', 'aprobada' => false],
        ['codigo' => 'en_revision', 'nombre' => 'En revisión', 'aprobada' => false],
        ['codigo' => 'aprobada', 'nombre' => 'Aprobada', 'aprobada' => true],
        ['codigo' => 'rechazada', 'nombre' => 'Rechazada', 'aprobada' => false],
    ];
}

function solicitud_estado_nombre(string $codigo): string
{
    $norm = strtolower(trim($codigo));
    $legacy = [
        'en revisión' => 'en_revision',
        'en revision' => 'en_revision',
        'pendiente' => 'pendiente',
        'aprobada' => 'aprobada',
        'rechazada' => 'rechazada',
    ];
    if (isset($legacy[$norm])) {
        $codigo = $legacy[$norm];
    }
    foreach (diccionario_estados_solicitud() as $e) {
        if (($e['codigo'] ?? '') === $codigo) {
            return (string) $e['nombre'];
        }
    }
    return $codigo;
}

function solicitud_codigos_estado_validos(): array
{
    return array_column(diccionario_estados_solicitud(), 'codigo');
}
