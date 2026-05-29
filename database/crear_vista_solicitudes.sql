-- Vista unificada para phpMyAdmin: solicitudes de estudiantes con plazo del tipo.
USE solicitudes_academicas;

DROP VIEW IF EXISTS v_solicitudes_completa;

CREATE VIEW v_solicitudes_completa AS
SELECT
    s.id_solicitud,
    'Estudiante' AS tipo_radicante,
    s.id_estudiante,
    COALESCE(s.documento_estudiante, '') AS documento_estudiante,
    s.id_tipo_solicitud,
    t.codigo AS codigo_tipo_catalogo,
    t.nombre AS nombre_tipo_solicitud,
    t.plazo AS plazo_tipo_solicitud,
    s.codigo_tipo,
    s.fecha_registro,
    s.estado,
    s.descripcion,
    COALESCE(s.respuesta, '') AS respuesta,
    s.fecha_respuesta,
    s.respondido_en,
    de.id_estudiantil,
    de.programa_nombre,
    de.estado_academico_label,
    de.semestre,
    de.periodo_academico,
    de.id_sede_solicitud,
    de.id_jornada_solicitud,
    de.motivo_label,
    de.exposicion,
    re.numero_respuesta,
    re.decision AS decision_formal,
    re.justificacion AS justificacion_formal
FROM solicitudes s
LEFT JOIN tipos_solicitud_estudiante t ON t.id_tipo_solicitud = s.id_tipo_solicitud
LEFT JOIN solicitud_detalle_estudiante de ON de.id_solicitud = s.id_solicitud
LEFT JOIN solicitud_respuesta_elaborada re ON re.id_solicitud = s.id_solicitud;
