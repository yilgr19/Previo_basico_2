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

function estado_academico_estudiante_nombre(?string $cod): string
{
    $cod = strtoupper(trim((string) $cod));
    $map = [
        'PAI' => 'PAI (Programa de Acompañamiento)',
        'REGULAR' => 'Regular',
        'PRUEBA' => 'Prueba académica',
        'EGRESADO' => 'Egresado',
    ];
    if ($cod !== '' && isset($map[$cod])) {
        return $map[$cod];
    }
    return $cod !== '' ? $cod : '—';
}

/** Motivos estadísticos (solicitud estudiantil). */
function diccionario_motivos_solicitud_estudiante(): array
{
    return [
        ['codigo' => 'salud', 'nombre' => 'Salud'],
        ['codigo' => 'economicos', 'nombre' => 'Económicos'],
        ['codigo' => 'cruce_horarios', 'nombre' => 'Cruce de horarios'],
        ['codigo' => 'cambio_residencia', 'nombre' => 'Cambio de residencia'],
        ['codigo' => 'otro', 'nombre' => 'Otro'],
    ];
}

function motivo_solicitud_estudiante_nombre(?string $cod): string
{
    $cod = strtolower(trim((string) $cod));
    foreach (diccionario_motivos_solicitud_estudiante() as $m) {
        if (($m['codigo'] ?? '') === $cod) {
            return (string) $m['nombre'];
        }
    }
    return $cod !== '' ? $cod : '—';
}

/**
 * Catálogo de solicitudes docente (académico, laboral e investigativo; distinto del estudiantil).
 *
 * @return array<int, array{id: int, codigo: string, nombre: string}>
 */
function diccionario_tipos_solicitud_docente(): array
{
    return [
        ['id' => 1, 'codigo' => 'DOC_RECT_ACTA', 'nombre' => 'Rectificación de Acta de Calificaciones'],
        ['id' => 2, 'codigo' => 'DOC_PERM_LIC', 'nombre' => 'Permiso Remunerado / Licencia Corta'],
        ['id' => 3, 'codigo' => 'DOC_COM_EST', 'nombre' => 'Comisión de Estudios o Servicios'],
        ['id' => 4, 'codigo' => 'DOC_CERT_LAB', 'nombre' => 'Certificado Laboral y de Ingresos'],
        ['id' => 5, 'codigo' => 'DOC_MOD_CARGA', 'nombre' => 'Modificación de Carga Académica'],
        ['id' => 6, 'codigo' => 'DOC_DESC_INV', 'nombre' => 'Solicitud de Descarga por Investigación'],
        ['id' => 7, 'codigo' => 'DOC_RES_ESP', 'nombre' => 'Reserva de Espacios de Aprendizaje'],
        ['id' => 8, 'codigo' => 'DOC_MON_ASI', 'nombre' => 'Asignación de Monitor o Asistente'],
        ['id' => 9, 'codigo' => 'DOC_REPROG_EVAL', 'nombre' => 'Reprogramación de Evaluaciones'],
        ['id' => 10, 'codigo' => 'DOC_ANO_SAB', 'nombre' => 'Solicitud de Año Sabático'],
        ['id' => 11, 'codigo' => 'DOC_NOV_NOM', 'nombre' => 'Reporte de Novedades de Nómina'],
        ['id' => 12, 'codigo' => 'DOC_INS_EQUIP', 'nombre' => 'Solicitud de Insumos o Equipos'],
        ['id' => 13, 'codigo' => 'DOC_ASC_ESC', 'nombre' => 'Postulación a Ascenso en Escalafón'],
        ['id' => 14, 'codigo' => 'DOC_SAL_PED', 'nombre' => 'Solicitud de Salida Pedagógica'],
        ['id' => 15, 'codigo' => 'DOC_OTRA', 'nombre' => 'Otra / Petición General'],
    ];
}

function tipo_solicitud_docente_por_id(int $id): ?array
{
    foreach (diccionario_tipos_solicitud_docente() as $t) {
        if ((int) ($t['id'] ?? 0) === $id) {
            return $t;
        }
    }
    return null;
}

function tipo_solicitud_docente_nombre(int $id): string
{
    $t = tipo_solicitud_docente_por_id($id);
    return $t ? (string) $t['nombre'] : '—';
}

function diccionario_prioridad_solicitud_docente(): array
{
    return [
        ['codigo' => 'baja', 'nombre' => 'Baja'],
        ['codigo' => 'media', 'nombre' => 'Media'],
        ['codigo' => 'alta', 'nombre' => 'Alta'],
    ];
}

function prioridad_solicitud_docente_nombre(?string $cod): string
{
    $cod = strtolower(trim((string) $cod));
    foreach (diccionario_prioridad_solicitud_docente() as $p) {
        if (($p['codigo'] ?? '') === $cod) {
            return (string) $p['nombre'];
        }
    }
    return $cod !== '' ? $cod : '—';
}

function diccionario_categoria_docente(): array
{
    return [
        ['codigo' => 'auxiliar', 'nombre' => 'Auxiliar'],
        ['codigo' => 'asistente', 'nombre' => 'Asistente'],
        ['codigo' => 'asociado', 'nombre' => 'Asociado'],
        ['codigo' => 'titular', 'nombre' => 'Titular'],
    ];
}

function categoria_docente_nombre(?string $cod): string
{
    $cod = strtolower(trim((string) $cod));
    foreach (diccionario_categoria_docente() as $c) {
        if (($c['codigo'] ?? '') === $cod) {
            return (string) $c['nombre'];
        }
    }
    return $cod !== '' ? $cod : '—';
}

function diccionario_tipo_contrato_docente(): array
{
    return [
        ['codigo' => 'tiempo_completo', 'nombre' => 'Tiempo completo'],
        ['codigo' => 'medio_tiempo', 'nombre' => 'Medio tiempo'],
        ['codigo' => 'catedra', 'nombre' => 'Cátedra'],
        ['codigo' => 'otro', 'nombre' => 'Otro'],
    ];
}

function tipo_contrato_docente_nombre(?string $cod): string
{
    $cod = strtolower(trim((string) $cod));
    foreach (diccionario_tipo_contrato_docente() as $c) {
        if (($c['codigo'] ?? '') === $cod) {
            return (string) $c['nombre'];
        }
    }
    return $cod !== '' ? $cod : '—';
}

function solicitud_es_radicada_docente(array $s): bool
{
    return (int) ($s['id_docente_solicitante'] ?? 0) > 0;
}

function solicitud_tipo_etiqueta(array $s): string
{
    if (solicitud_es_radicada_docente($s)) {
        $idTd = (int) ($s['id_tipo_solicitud_docente'] ?? 0);
        if ($idTd > 0) {
            return tipo_solicitud_docente_nombre($idTd);
        }
        return tipo_solicitud_nombre((int) ($s['id_tipo_solicitud'] ?? 0));
    }
    return tipo_solicitud_nombre((int) ($s['id_tipo_solicitud'] ?? 0));
}

/** Texto principal para listados (exposición o descripción según formulario). */
function solicitud_resumen_texto(array $s): string
{
    $de = $s['detalle_estudiante'] ?? null;
    if (is_array($de) && !empty($de['cuerpo']['exposicion'])) {
        return (string) $de['cuerpo']['exposicion'];
    }
    $dd = $s['detalle_docente'] ?? null;
    if (is_array($dd) && !empty($dd['cuerpo']['descripcion_detallada'])) {
        return (string) $dd['cuerpo']['descripcion_detallada'];
    }
    return (string) ($s['descripcion'] ?? '');
}

/** Etiqueta legible para la categoría de un adjunto (evidencias). */
function solicitud_etiqueta_categoria_anexo(?string $categoria): string
{
    $c = strtolower(trim((string) $categoria));
    $map = [
        'general' => 'General',
        'soporte_medico' => 'Soporte médico',
        'carta_aceptacion' => 'Carta de aceptación',
        'recibo_pago' => 'Recibo de pago',
        'doc_terceros' => 'Documentación de terceros',
        'formato_institucional' => 'Formato institucional',
    ];
    return $map[$c] ?? ($c !== '' ? $c : '—');
}

/**
 * Decisiones narrativas para la resolución formal (carta institucional).
 *
 * @return array<int, array{codigo: string, nombre: string}>
 */
function diccionario_decision_resolucion_formal(): array
{
    return [
        ['codigo' => 'aprobado', 'nombre' => 'Aprobado — la solicitud sigue un curso favorable'],
        ['codigo' => 'rechazado', 'nombre' => 'Rechazado — no cumple requisitos'],
        ['codigo' => 'pendiente_informacion', 'nombre' => 'Pendiente de información (subsanación)'],
    ];
}

function solicitud_decision_resolucion_nombre(?string $cod): string
{
    $cod = strtolower(trim((string) $cod));
    foreach (diccionario_decision_resolucion_formal() as $d) {
        if (($d['codigo'] ?? '') === $cod) {
            return (string) $d['nombre'];
        }
    }
    return $cod !== '' ? $cod : '—';
}

/**
 * Fecha/hora de cierre de respuesta institucional (vacío = aún no contestada o sin migrar).
 */
function solicitud_tiene_respuesta_cerrada(array $s): bool
{
    return trim((string) ($s['respondido_en'] ?? '')) !== '';
}

/**
 * Texto para mostrar al usuario: prioriza fecha y hora exactas de cierre.
 */
function solicitud_texto_momento_respuesta(?array $s): string
{
    if ($s === null) {
        return '';
    }
    $t = trim((string) ($s['respondido_en'] ?? ''));
    if ($t !== '' && preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $t)) {
        return $t;
    }
    $fr = trim((string) ($s['fecha_respuesta'] ?? ''));
    return $fr !== '' ? $fr : '';
}

/**
 * Si ya había texto de respuesta en datos viejos sin `respondido_en`, infiere un cierre para bloquear ediciones.
 */
function solicitud_inferir_respondido_en_legacy(array $s): string
{
    $txt = trim((string) ($s['respuesta'] ?? ''));
    $elab = $s['respuesta_elaborada'] ?? null;
    $emElab = is_array($elab) ? trim((string) ($elab['emitido_en'] ?? '')) : '';
    $tieneElab = is_array($elab) && (
        $emElab !== ''
        || trim((string) ($elab['numero_respuesta'] ?? '')) !== ''
        || trim((string) ($elab['justificacion'] ?? '')) !== ''
    );
    if ($txt === '' && !$tieneElab) {
        return '';
    }
    if ($emElab !== '' && preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $emElab)) {
        return $emElab;
    }
    $fr = trim((string) ($s['fecha_respuesta'] ?? ''));
    if ($fr !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fr)) {
        return $fr . ' 12:00:00';
    }
    $reg = trim((string) ($s['fecha_registro'] ?? ''));
    if ($reg !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $reg)) {
        return $reg . ' 12:00:00';
    }

    return date('Y-m-d') . ' 12:00:00';
}

/**
 * Construye el arreglo persistido de respuesta elaborada desde POST (panel gestión).
 *
 * @param array<string, mixed>|null $anterior Valores previos para conservar número correlativo.
 * @param string|null $emitidoEn Fecha y hora ISO (misma marca que el cierre de la solicitud).
 *
 * @return array<string, mixed>
 */
function solicitud_respuesta_elaborada_desde_post(int $idSolicitud, ?array $anterior, ?string $emitidoEn = null): array
{
    $num = '';
    if (is_array($anterior) && trim((string) ($anterior['numero_respuesta'] ?? '')) !== '') {
        $num = trim((string) $anterior['numero_respuesta']);
    } else {
        $num = 'RES-' . date('Y') . '-' . str_pad((string) $idSolicitud, 5, '0', STR_PAD_LEFT);
    }
    $dec = strtolower(trim((string) ($_POST['elab_decision'] ?? '')));
    $valid = array_column(diccionario_decision_resolucion_formal(), 'codigo');
    if (!in_array($dec, $valid, true)) {
        $dec = 'pendiente_informacion';
    }
    $emit = $emitidoEn ?? fecha_hora_colombia();

    return [
        'numero_respuesta' => $num,
        'id_solicitud' => $idSolicitud,
        'emitido_en' => $emit,
        'decision' => $dec,
        'justificacion' => trim((string) ($_POST['elab_justificacion'] ?? '')),
        'normativas' => trim((string) ($_POST['elab_normativas'] ?? '')),
        'subsanacion_items' => trim((string) ($_POST['elab_subsanacion_items'] ?? '')),
        'subsanacion_error_doc' => trim((string) ($_POST['elab_subsanacion_error_doc'] ?? '')),
        'subsanacion_fecha_limite' => trim((string) ($_POST['elab_subsanacion_fecha_limite'] ?? '')),
        'instrucciones_cierre' => trim((string) ($_POST['elab_instrucciones_cierre'] ?? '')),
        'recursos_apelacion' => trim((string) ($_POST['elab_recursos_apelacion'] ?? '')),
        'funcionario_nombre' => trim((string) ($_POST['elab_funcionario_nombre'] ?? '')),
        'funcionario_cargo' => trim((string) ($_POST['elab_funcionario_cargo'] ?? '')),
        'codigo_verificacion' => trim((string) ($_POST['elab_codigo_verificacion'] ?? '')),
    ];
}
