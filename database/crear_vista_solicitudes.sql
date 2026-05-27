-- Vista unificada para phpMyAdmin: toda la información de cada solicitud en una fila.
USE solicitudes_academicas;

DROP VIEW IF EXISTS v_solicitudes_completa;

CREATE VIEW v_solicitudes_completa AS
SELECT
    s.id_solicitud,
    CASE
        WHEN s.id_estudiante IS NOT NULL THEN 'Estudiante'
        WHEN s.id_docente_solicitante IS NOT NULL THEN 'Docente'
        ELSE 'Desconocido'
    END AS tipo_radicante,
    s.id_estudiante,
    s.id_docente_solicitante,
    COALESCE(s.documento_estudiante, '') AS documento_estudiante,
    COALESCE(s.documento_docente_relacionado, '') AS documento_docente_relacionado,
    s.id_tipo_solicitud,
    s.id_tipo_solicitud_docente,
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
    dd.nombre_completo AS docente_nombre,
    dd.asunto,
    dd.prioridad_label,
    dd.nrc,
    dd.nombre_materia,
    dd.horario_impactado,
    dd.descripcion_detallada,
    dd.sustento_legal,
    dd.fecha_inicio,
    dd.fecha_fin,
    re.numero_respuesta,
    re.decision AS decision_formal,
    re.justificacion AS justificacion_formal
FROM solicitudes s
LEFT JOIN solicitud_detalle_estudiante de ON de.id_solicitud = s.id_solicitud
LEFT JOIN solicitud_detalle_docente dd ON dd.id_solicitud = s.id_solicitud
LEFT JOIN solicitud_respuesta_elaborada re ON re.id_solicitud = s.id_solicitud;
