-- Plazos personalizables por tipo de solicitud y sede (días).
-- Ejecutar en phpMyAdmin sobre la base solicitudes_academicas.

USE solicitudes_academicas;

CREATE TABLE IF NOT EXISTS tipo_solicitud_plazo_sede (
  id_tipo_solicitud tinyint(3) UNSIGNED NOT NULL,
  id_sede tinyint(3) UNSIGNED NOT NULL,
  plazo smallint(5) UNSIGNED NOT NULL COMMENT 'Plazo en días',
  PRIMARY KEY (id_tipo_solicitud, id_sede),
  KEY fk_tps_sede (id_sede),
  CONSTRAINT fk_tps_tipo FOREIGN KEY (id_tipo_solicitud) REFERENCES tipos_solicitud_estudiante (id_tipo_solicitud) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_tps_sede_ref FOREIGN KEY (id_sede) REFERENCES sedes (id_sede) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Opcional: copiar valores del texto en tipos_solicitud_estudiante.plazo (solo sede Cúcuta id=1)
INSERT INTO tipo_solicitud_plazo_sede (id_tipo_solicitud, id_sede, plazo)
SELECT
    t.id_tipo_solicitud,
    1 AS id_sede,
    CAST(REGEXP_SUBSTR(t.plazo, '[0-9]+') AS UNSIGNED) AS plazo
FROM tipos_solicitud_estudiante t
WHERE REGEXP_SUBSTR(t.plazo, '[0-9]+') IS NOT NULL
  AND CAST(REGEXP_SUBSTR(t.plazo, '[0-9]+') AS UNSIGNED) > 0
ON DUPLICATE KEY UPDATE plazo = VALUES(plazo);

INSERT INTO tipo_solicitud_plazo_sede (id_tipo_solicitud, id_sede, plazo)
SELECT
    t.id_tipo_solicitud,
    2 AS id_sede,
    CAST(REGEXP_SUBSTR(t.plazo, '[0-9]+') AS UNSIGNED) AS plazo
FROM tipos_solicitud_estudiante t
WHERE REGEXP_SUBSTR(t.plazo, '[0-9]+') IS NOT NULL
  AND CAST(REGEXP_SUBSTR(t.plazo, '[0-9]+') AS UNSIGNED) > 0
ON DUPLICATE KEY UPDATE plazo = VALUES(plazo);
