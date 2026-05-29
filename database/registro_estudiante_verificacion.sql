-- Autoregistro: solicitudes pendientes hasta verificar correo institucional.
-- Ejecutar en phpMyAdmin sobre solicitudes_academicas.

USE solicitudes_academicas;

CREATE TABLE IF NOT EXISTS registro_estudiante_pendiente (
  id_pendiente int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  token_hash char(64) NOT NULL COMMENT 'SHA-256 del token enviado por correo',
  correo varchar(120) NOT NULL,
  documento varchar(20) NOT NULL,
  datos_json longtext NOT NULL COMMENT 'Datos del formulario (sin contraseña)',
  clave varchar(255) NOT NULL COMMENT 'Temporal hasta verificar; luego se mueve a estudiantes',
  creado_en datetime NOT NULL,
  expira_en datetime NOT NULL,
  PRIMARY KEY (id_pendiente),
  UNIQUE KEY uk_rep_token (token_hash),
  UNIQUE KEY uk_rep_correo (correo),
  UNIQUE KEY uk_rep_documento (documento),
  KEY idx_rep_expira (expira_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
