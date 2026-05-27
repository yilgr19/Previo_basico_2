-- Ejecutar en phpMyAdmin (pestaña SQL, servidor 127.0.0.1):
-- 1) Este archivo crea la base de datos.
-- 2) Luego importe database/solicitudes_academicas.sql (tablas, datos y claves foráneas).

CREATE DATABASE IF NOT EXISTS solicitudes_academicas
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE solicitudes_academicas;