-- =============================================================================
-- Migración: eliminar módulo docente + columna plazo en tipos de solicitud
-- Base: solicitudes_academicas
-- Ejecutar en phpMyAdmin o: mysql -u root solicitudes_academicas < migracion_sin_docentes_y_plazo.sql
-- =============================================================================

USE solicitudes_academicas;

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- -----------------------------------------------------------------------------
-- 1) Eliminar solicitudes y datos ligados a radicación docente
-- -----------------------------------------------------------------------------
DELETE re
FROM solicitud_respuesta_elaborada re
INNER JOIN solicitudes s ON s.id_solicitud = re.id_solicitud
WHERE s.id_docente_solicitante IS NOT NULL;

DELETE ax
FROM solicitud_anexos ax
INNER JOIN solicitudes s ON s.id_solicitud = ax.id_solicitud
WHERE s.id_docente_solicitante IS NOT NULL;

DELETE FROM solicitud_detalle_docente
WHERE id_solicitud IN (
    SELECT id_solicitud FROM (
        SELECT id_solicitud FROM solicitudes WHERE id_docente_solicitante IS NOT NULL
    ) AS tmp
);

DELETE FROM solicitudes WHERE id_docente_solicitante IS NOT NULL;

-- -----------------------------------------------------------------------------
-- 2) Quitar columnas docente de solicitudes
-- -----------------------------------------------------------------------------
-- (Si el nombre de la FK difiere, ejecute: SHOW CREATE TABLE solicitudes;)
ALTER TABLE solicitudes DROP FOREIGN KEY fk_sol_docente;

ALTER TABLE solicitudes
    DROP INDEX idx_sol_docente,
    DROP INDEX idx_sol_tipo_doc;

ALTER TABLE solicitudes
    DROP COLUMN id_docente_solicitante,
    DROP COLUMN id_tipo_solicitud_docente,
    DROP COLUMN documento_docente_relacionado,
    DROP COLUMN notif_pendiente_doc;

ALTER TABLE solicitudes
    MODIFY COLUMN id_estudiante int(10) UNSIGNED NOT NULL,
    MODIFY COLUMN id_tipo_solicitud tinyint(3) UNSIGNED NOT NULL;

-- -----------------------------------------------------------------------------
-- 3) Eliminar tablas del módulo docente
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS solicitud_detalle_docente;
DROP TABLE IF EXISTS docentes;
DROP TABLE IF EXISTS tipos_solicitud_docente;
DROP TABLE IF EXISTS prioridades_solicitud_docente;
DROP TABLE IF EXISTS categorias_docente;
DROP TABLE IF EXISTS tipos_contrato_docente;

-- -----------------------------------------------------------------------------
-- 4) Plazo en tipos de solicitud estudiante
-- -----------------------------------------------------------------------------
ALTER TABLE tipos_solicitud_estudiante
    ADD COLUMN plazo varchar(100) NOT NULL DEFAULT '15 días hábiles'
        COMMENT 'Plazo institucional de respuesta o trámite'
        AFTER nombre;

UPDATE tipos_solicitud_estudiante SET plazo = '15 días hábiles' WHERE id_tipo_solicitud = 1;
UPDATE tipos_solicitud_estudiante SET plazo = '20 días hábiles' WHERE id_tipo_solicitud = 2;
UPDATE tipos_solicitud_estudiante SET plazo = '10 días hábiles' WHERE id_tipo_solicitud = 3;
UPDATE tipos_solicitud_estudiante SET plazo = '15 días hábiles' WHERE id_tipo_solicitud = 4;
UPDATE tipos_solicitud_estudiante SET plazo = '30 días hábiles' WHERE id_tipo_solicitud = 5;
UPDATE tipos_solicitud_estudiante SET plazo = '20 días hábiles' WHERE id_tipo_solicitud = 6;
UPDATE tipos_solicitud_estudiante SET plazo = '30 días hábiles' WHERE id_tipo_solicitud = 7;
UPDATE tipos_solicitud_estudiante SET plazo = '10 días hábiles' WHERE id_tipo_solicitud = 8;
UPDATE tipos_solicitud_estudiante SET plazo = '30 días hábiles' WHERE id_tipo_solicitud = 9;
UPDATE tipos_solicitud_estudiante SET plazo = '10 días hábiles' WHERE id_tipo_solicitud = 10;
UPDATE tipos_solicitud_estudiante SET plazo = '5 días hábiles' WHERE id_tipo_solicitud = 11;
UPDATE tipos_solicitud_estudiante SET plazo = '5 días hábiles' WHERE id_tipo_solicitud = 12;
UPDATE tipos_solicitud_estudiante SET plazo = '15 días hábiles' WHERE id_tipo_solicitud = 13;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- 5) Vista unificada (sin docentes)
-- -----------------------------------------------------------------------------
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

-- Fin migración
