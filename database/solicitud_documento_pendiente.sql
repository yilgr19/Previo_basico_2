-- Solicitudes de documentos por parte de gestión académica al estudiante.
-- Ejecutar en phpMyAdmin sobre solicitudes_academicas.

USE solicitudes_academicas;

CREATE TABLE IF NOT EXISTS solicitud_documento_pendiente (
  id_documento_pendiente int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  id_solicitud int(10) UNSIGNED NOT NULL,
  categoria varchar(50) NOT NULL,
  mensaje text NOT NULL,
  solicitado_en datetime NOT NULL,
  cumplido_en datetime DEFAULT NULL,
  notif_pendiente tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id_documento_pendiente),
  KEY idx_sdp_solicitud (id_solicitud),
  KEY idx_sdp_abierto (id_solicitud, cumplido_en),
  CONSTRAINT fk_sdp_solicitud FOREIGN KEY (id_solicitud) REFERENCES solicitudes (id_solicitud) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
