-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-05-2026 a las 01:57:20
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS solicitudes_academicas
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE solicitudes_academicas;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `solicitudes_academicas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id_admin` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `rol` varchar(40) NOT NULL DEFAULT 'Administrador',
  `clave` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id_admin`, `nombre`, `correo`, `rol`, `clave`) VALUES
(1, 'Administrador Principal', 'admin@academico.edu', 'Administrador', 'admin123');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_docente`
--

CREATE TABLE `categorias_docente` (
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias_docente`
--

INSERT INTO `categorias_docente` (`codigo`, `nombre`) VALUES
('asistente', 'Asistente'),
('asociado', 'Asociado'),
('auxiliar', 'Auxiliar'),
('titular', 'Titular');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `decisiones_resolucion_formal`
--

CREATE TABLE `decisiones_resolucion_formal` (
  `codigo` varchar(30) NOT NULL,
  `nombre` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `decisiones_resolucion_formal`
--

INSERT INTO `decisiones_resolucion_formal` (`codigo`, `nombre`) VALUES
('aprobado', 'Aprobado — la solicitud sigue un curso favorable'),
('pendiente_informacion', 'Pendiente de información (subsanación)'),
('rechazado', 'Rechazado — no cumple requisitos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docentes`
--

CREATE TABLE `docentes` (
  `id_docente` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `id_sede` tinyint(3) UNSIGNED DEFAULT NULL,
  `id_programa` smallint(5) UNSIGNED DEFAULT NULL,
  `programa` varchar(200) DEFAULT NULL,
  `codigo_empleado` varchar(30) DEFAULT NULL,
  `unidad_academica` varchar(200) DEFAULT NULL,
  `categoria_docente` varchar(20) DEFAULT NULL,
  `tipo_contrato` varchar(20) DEFAULT NULL,
  `clave` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `docentes`
--

INSERT INTO `docentes` (`id_docente`, `nombre`, `apellido`, `documento`, `correo`, `telefono`, `id_sede`, `id_programa`, `programa`, `codigo_empleado`, `unidad_academica`, `categoria_docente`, `tipo_contrato`, `clave`) VALUES
(1, 'Juliana Daniela', 'Orozco', '111111', 'j.orozco@universidad.edu.co', '3001112233', 1, 121, '[91388] Diseño Gráfico', 'EMP-CC-5001', 'Cúcuta — Facultad de Diseño', 'asociado', 'tiempo_completo', 'demo123'),
(2, 'Carlos Andrés', 'Méndez', '201002', 'c.mendez@universidad.edu.co', '3002223344', 1, 124, '[107860] Tecnología en Desarrollo de Software', 'EMP-CC-5002', 'Cúcuta — Ingeniería de sistemas', 'titular', 'tiempo_completo', 'demo123'),
(3, 'Ricardo Esteban', 'Pardo', '212001', 'r.pardo.ocana@universidad.edu.co', '3011112233', 2, 128, '[102041] Diseño Gráfico Ocaña', 'EMP-OC-6001', 'Extensión Ocaña — Diseño y comunicación visual', 'asociado', 'tiempo_completo', 'demo123'),
(4, 'Mónica Liliana', 'Fuentes', '212002', 'm.fuentes.ocana@universidad.edu.co', '3012223344', 2, 127, '[108788] Tecn. en Gestión de Contenidos Gráficos Public. Ocaña', 'EMP-OC-6002', 'Extensión Ocaña — Contenidos gráficos y multimedia', 'titular', 'tiempo_completo', 'demo123');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_solicitud`
--

CREATE TABLE `estados_solicitud` (
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(40) NOT NULL,
  `aprobada` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estados_solicitud`
--

INSERT INTO `estados_solicitud` (`codigo`, `nombre`, `aprobada`) VALUES
('aprobada', 'Aprobada', 1),
('en_revision', 'En revisión', 0),
('pendiente', 'Pendiente', 0),
('rechazada', 'Rechazada', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `id_estudiante` int(10) UNSIGNED NOT NULL,
  `tipo_identificacion` char(3) NOT NULL DEFAULT 'CC',
  `documento` varchar(20) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `sexo` char(1) DEFAULT NULL,
  `id_programa` smallint(5) UNSIGNED DEFAULT NULL,
  `programa` varchar(200) DEFAULT NULL COMMENT 'Etiqueta denormalizada [c??digo] nombre',
  `estado_academico` varchar(20) NOT NULL DEFAULT 'REGULAR' COMMENT 'PAI, REGULAR, PRUEBA, EGRESADO',
  `semestre` tinyint(3) UNSIGNED DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `edad` tinyint(3) UNSIGNED DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `barrio` varchar(80) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `id_sede` tinyint(3) UNSIGNED DEFAULT NULL,
  `id_jornada` tinyint(3) UNSIGNED DEFAULT NULL,
  `clave` varchar(255) NOT NULL COMMENT 'En producci??n: hash, no texto plano'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`id_estudiante`, `tipo_identificacion`, `documento`, `nombre`, `apellido`, `correo`, `sexo`, `id_programa`, `programa`, `estado_academico`, `semestre`, `fecha_nacimiento`, `edad`, `direccion`, `barrio`, `telefono`, `id_sede`, `id_jornada`, `clave`) VALUES
(1, 'CC', '301001', 'Andrea', 'López', 'a.lopez@estudiante.edu.co', 'F', 124, '[107860] Tecnología en Desarrollo de Software', 'REGULAR', 4, '2003-04-12', 22, 'Calle 15 #8-40', 'La Playa', '3101002001', 1, 1, 'demo123'),
(2, 'CC', '301002', 'Brayan', 'Castro', 'b.castro@estudiante.edu.co', 'M', 124, '[107860] Tecnología en Desarrollo de Software', 'REGULAR', 3, '2004-11-20', 21, 'Av. Libertadores 102', 'Centro', '3101002002', 1, 1, 'demo123'),
(3, 'CC', '301006', 'Valentina', 'Rincón', 'v.rincon.ocana@estudiante.edu.co', 'F', 128, '[102041] Diseño Gráfico Ocaña', 'REGULAR', 4, '2003-06-01', 22, 'Carrera 10 #5-20', 'Centro — Ocaña', '3101002006', 2, 1, 'demo123'),
(4, 'CC', '301007', 'Jhon Jairo', 'Patiño', 'j.patino.ocana@estudiante.edu.co', 'M', 127, '[108788] Tecn. en Gestión de Contenidos Gráficos Public. Ocaña', 'REGULAR', 3, '2004-03-18', 21, 'Calle 14 #8-33', 'La Esperanza', '3101002007', 2, 1, 'demo123');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jornadas`
--

CREATE TABLE `jornadas` (
  `id_jornada` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `jornadas`
--

INSERT INTO `jornadas` (`id_jornada`, `nombre`) VALUES
(1, 'Diurna'),
(2, 'Nocturna'),
(3, 'Distancia'),
(4, 'Virtual');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `motivos_solicitud_estudiante`
--

CREATE TABLE `motivos_solicitud_estudiante` (
  `codigo` varchar(30) NOT NULL,
  `nombre` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `motivos_solicitud_estudiante`
--

INSERT INTO `motivos_solicitud_estudiante` (`codigo`, `nombre`) VALUES
('cambio_residencia', 'Cambio de residencia'),
('cruce_horarios', 'Cruce de horarios'),
('economicos', 'Económicos'),
('otro', 'Otro'),
('salud', 'Salud');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prioridades_solicitud_docente`
--

CREATE TABLE `prioridades_solicitud_docente` (
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `prioridades_solicitud_docente`
--

INSERT INTO `prioridades_solicitud_docente` (`codigo`, `nombre`) VALUES
('alta', 'Alta'),
('baja', 'Baja'),
('media', 'Media');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programas`
--

CREATE TABLE `programas` (
  `id_programa` smallint(5) UNSIGNED NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `id_sede` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `programas`
--

INSERT INTO `programas` (`id_programa`, `codigo`, `nombre`, `id_sede`) VALUES
(117, '90604', 'Técnica Profesional en Operaciones Logísticas', 1),
(118, '90605', 'Tecnología en Gestión Logística Empresarial', 1),
(119, '91390', 'Técnica Profesional en Producción Gráfica', 1),
(120, '107156', 'Tecn. en Gestión de Contenidos Gráficos Publicitarios', 1),
(121, '91388', 'Diseño Gráfico', 1),
(123, '107859', 'Técnica Profesional en Soporte Informático', 1),
(124, '107860', 'Tecnología en Desarrollo de Software', 1),
(125, '107861', 'Ingeniería de Software', 1),
(126, '107858', 'Especialización en Gestión Pública', 1),
(127, '108788', 'Tecn. en Gestión de Contenidos Gráficos Public. Ocaña', 2),
(128, '102041', 'Diseño Gráfico Ocaña', 2),
(130, '102517', 'Tecn. en Gestión de Negocios Internacionales Ocaña', 2),
(131, '102518', 'Administración de Negocios Internacionales Ocaña', 2),
(133, '102887', 'Técnica Prof. en Operaciones Turísticas Virtual', 1),
(134, '111410', 'Tecnología en Gestión del Turismo Sostenible Virtual', 1),
(137, '111412', 'Tecnología en Gestión del Turismo Sostenible Presencial', 1),
(143, '54348', 'Técnica Profesional en Procesos Contables Presencial', 1),
(153, '116880', 'Administración de Negocios Internacionales Presencial', 1),
(159, '104671', 'Profesional en Diseño y Administración de Negocios de la Moda', 1),
(166, '117775', 'Especialización en Analítica de Datos para los Negocios Virtual', 1),
(176, '91388', 'Profesional en Diseño Gráfico', 1),
(177, '118275', 'Especialización en Marketing Digital Estratégico Presencial', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sedes`
--

CREATE TABLE `sedes` (
  `id_sede` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sedes`
--

INSERT INTO `sedes` (`id_sede`, `nombre`) VALUES
(1, 'Cúcuta'),
(2, 'Ocaña');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sexos`
--

CREATE TABLE `sexos` (
  `codigo` char(1) NOT NULL,
  `nombre` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sexos`
--

INSERT INTO `sexos` (`codigo`, `nombre`) VALUES
('F', 'Femenino'),
('M', 'Masculino'),
('O', 'Otro / Prefiero no indicar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id_solicitud` int(10) UNSIGNED NOT NULL,
  `id_estudiante` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL si radica docente (en JSON era 0)',
  `id_docente_solicitante` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL si radica estudiante (en JSON era 0)',
  `documento_estudiante` varchar(20) DEFAULT NULL,
  `id_tipo_solicitud` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'NULL si es solicitud docente (en JSON era 0)',
  `id_tipo_solicitud_docente` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'NULL si es solicitud estudiante (en JSON era 0)',
  `codigo_tipo` varchar(30) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL COMMENT 'PHP: fecha_hora_colombia()',
  `estado` varchar(20) NOT NULL DEFAULT 'pendiente',
  `descripcion` text DEFAULT NULL COMMENT 'Resumen / exposici??n o descripci??n',
  `documento_docente_relacionado` varchar(20) DEFAULT NULL COMMENT 'Legacy; ya no se usa en formularios',
  `respuesta` text DEFAULT NULL COMMENT 'Respuesta breve del administrador',
  `fecha_respuesta` date DEFAULT NULL,
  `respondido_en` datetime DEFAULT NULL COMMENT 'Primera vez que se cerr?? respuesta',
  `formulario_version` tinyint(3) UNSIGNED NOT NULL DEFAULT 2,
  `notif_pendiente_est` tinyint(1) NOT NULL DEFAULT 0,
  `notif_pendiente_doc` tinyint(1) NOT NULL DEFAULT 0,
  `notif_nueva_gestion` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`id_solicitud`, `id_estudiante`, `id_docente_solicitante`, `documento_estudiante`, `id_tipo_solicitud`, `id_tipo_solicitud_docente`, `codigo_tipo`, `fecha_registro`, `estado`, `descripcion`, `documento_docente_relacionado`, `respuesta`, `fecha_respuesta`, `respondido_en`, `formulario_version`, `notif_pendiente_est`, `notif_pendiente_doc`, `notif_nueva_gestion`) VALUES
(1, 1, NULL, '301001', 11, NULL, 'REQ_CONST_EST', '2026-04-10 00:00:00', 'en_revision', 'Constancia para beca; el profesor Carlos Andrés Méndez puede confirmar asistencia al módulo práctico.', '201002', NULL, '2026-04-15', '2026-04-15 13:48:42', 2, 0, 0, 0),
(2, NULL, 1, NULL, NULL, 3, 'DOC_COM_EST', '2026-04-14 00:00:00', 'rechazada', 'Comisión de estudios; coordinación con docente Carlos Andrés Méndez.', '201002', NULL, '2026-04-15', '2026-04-15 13:48:24', 2, 0, 1, 0),
(3, 1, NULL, '301001', 2, NULL, 'REQ_CURSO_DIR', '2026-04-15 00:00:00', 'pendiente', 'adkaj gksdnfl dfnk', '532', NULL, NULL, NULL, 2, 0, 0, 0),
(4, NULL, 2, NULL, NULL, 14, 'DOC_SAL_PED', '2026-04-15 00:00:00', 'pendiente', 'jandjkn jcnjnc hbsjsnd', NULL, NULL, NULL, NULL, 2, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_anexos`
--

CREATE TABLE `solicitud_anexos` (
  `id_anexo` int(10) UNSIGNED NOT NULL,
  `id_solicitud` int(10) UNSIGNED NOT NULL,
  `guardado` varchar(120) NOT NULL COMMENT 'Nombre en disco (ev_*.pdf)',
  `original` varchar(255) NOT NULL COMMENT 'Nombre original del usuario',
  `mime` varchar(80) DEFAULT NULL,
  `bytes` int(10) UNSIGNED DEFAULT NULL,
  `categoria` varchar(30) NOT NULL DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_detalle_docente`
--

CREATE TABLE `solicitud_detalle_docente` (
  `id_solicitud` int(10) UNSIGNED NOT NULL,
  `id_empleado` varchar(30) DEFAULT NULL,
  `unidad_academica` varchar(200) DEFAULT NULL,
  `categoria_docente` varchar(20) DEFAULT NULL,
  `categoria_docente_label` varchar(40) DEFAULT NULL,
  `tipo_contrato` varchar(20) DEFAULT NULL,
  `tipo_contrato_label` varchar(40) DEFAULT NULL,
  `documento` varchar(20) DEFAULT NULL,
  `nombre_completo` varchar(160) DEFAULT NULL,
  `asunto` varchar(200) DEFAULT NULL,
  `prioridad` varchar(10) DEFAULT NULL,
  `prioridad_label` varchar(20) DEFAULT NULL,
  `nrc` varchar(20) DEFAULT NULL,
  `nombre_materia` varchar(120) DEFAULT NULL,
  `horario_impactado` varchar(120) DEFAULT NULL,
  `plan_contingencia` text DEFAULT NULL,
  `descripcion_detallada` text DEFAULT NULL,
  `sustento_legal` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `consentimiento_responsabilidad` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitud_detalle_docente`
--

INSERT INTO `solicitud_detalle_docente` (`id_solicitud`, `id_empleado`, `unidad_academica`, `categoria_docente`, `categoria_docente_label`, `tipo_contrato`, `tipo_contrato_label`, `documento`, `nombre_completo`, `asunto`, `prioridad`, `prioridad_label`, `nrc`, `nombre_materia`, `horario_impactado`, `plan_contingencia`, `descripcion_detallada`, `sustento_legal`, `fecha_inicio`, `fecha_fin`, `consentimiento_responsabilidad`) VALUES
(2, 'EMP-CC-5001', 'Cúcuta — Facultad de Diseño', 'asociado', 'Asociado', 'tiempo_completo', 'Tiempo completo', '111111', 'Juliana Daniela Orozco', 'Comisión corta para actualización de syllabus', 'media', 'Media', '20456', 'Bases de datos aplicadas', 'Martes y jueves 8:00–10:00', 'Sesión asíncrona en plataforma y tutoría el viernes.', 'Solicito permiso para participar en comisión de estudios durante dos semanas. Coordinación con el docente Carlos Andrés Méndez para cubrir laboratorio.', 'Acuerdo interno de facultad 012/2025.', '2026-05-05', '2026-05-19', 1),
(4, 'EMP-CC-5002', 'Cúcuta — Ingeniería de sistemas', 'titular', 'Titular', 'tiempo_completo', 'Tiempo completo', '201002', 'Carlos Andrés Méndez', 'ldiushiuweh', 'media', 'Media', 'jkjsnksaj', 'calculo', 'jdjjd551', 'sisis', 'jandjkn jcnjnc hbsjsnd', NULL, '2026-04-15', '2026-04-18', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_detalle_estudiante`
--

CREATE TABLE `solicitud_detalle_estudiante` (
  `id_solicitud` int(10) UNSIGNED NOT NULL,
  `id_estudiantil` varchar(20) DEFAULT NULL COMMENT 'perfil_snapshot.id_estudiantil',
  `id_programa` smallint(5) UNSIGNED DEFAULT NULL,
  `programa_nombre` varchar(200) DEFAULT NULL,
  `estado_academico` varchar(20) DEFAULT NULL,
  `estado_academico_label` varchar(80) DEFAULT NULL,
  `semestre` tinyint(3) UNSIGNED DEFAULT NULL,
  `id_sede_matricula` tinyint(3) UNSIGNED DEFAULT NULL,
  `id_jornada_matricula` tinyint(3) UNSIGNED DEFAULT NULL,
  `periodo_academico` varchar(10) DEFAULT NULL COMMENT 'AAAA-S ej. 2026-1',
  `id_sede_solicitud` tinyint(3) UNSIGNED DEFAULT NULL,
  `id_jornada_solicitud` tinyint(3) UNSIGNED DEFAULT NULL,
  `motivo` varchar(30) DEFAULT NULL,
  `motivo_label` varchar(60) DEFAULT NULL,
  `exposicion` text DEFAULT NULL,
  `consentimiento_veracidad` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitud_detalle_estudiante`
--

INSERT INTO `solicitud_detalle_estudiante` (`id_solicitud`, `id_estudiantil`, `id_programa`, `programa_nombre`, `estado_academico`, `estado_academico_label`, `semestre`, `id_sede_matricula`, `id_jornada_matricula`, `periodo_academico`, `id_sede_solicitud`, `id_jornada_solicitud`, `motivo`, `motivo_label`, `exposicion`, `consentimiento_veracidad`) VALUES
(1, '301001', 124, '[107860] Tecnología en Desarrollo de Software', 'REGULAR', 'Regular', 4, 1, 1, '2026-1', 1, 1, 'economicos', 'Económicos', 'Requiero constancia de estudio para trámite de beca municipal. Indico como referencia académica al docente Carlos Andrés Méndez (documento 201002), quien orientó el proyecto integrador del periodo.', 1),
(3, '301001', 124, '[107860] Tecnología en Desarrollo de Software', 'REGULAR', 'Regular', 4, 1, 1, '2026-1', 1, 4, 'economicos', 'Económicos', 'adkaj gksdnfl dfnk', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_respuesta_elaborada`
--

CREATE TABLE `solicitud_respuesta_elaborada` (
  `id_solicitud` int(10) UNSIGNED NOT NULL,
  `numero_respuesta` varchar(30) NOT NULL,
  `emitido_en` datetime NOT NULL,
  `decision` varchar(30) NOT NULL,
  `justificacion` text DEFAULT NULL,
  `normativas` text DEFAULT NULL,
  `subsanacion_items` text DEFAULT NULL,
  `subsanacion_error_doc` text DEFAULT NULL,
  `subsanacion_fecha_limite` varchar(30) DEFAULT NULL,
  `instrucciones_cierre` text DEFAULT NULL,
  `recursos_apelacion` text DEFAULT NULL,
  `funcionario_nombre` varchar(120) DEFAULT NULL,
  `funcionario_cargo` varchar(120) DEFAULT NULL,
  `codigo_verificacion` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitud_respuesta_elaborada`
--

INSERT INTO `solicitud_respuesta_elaborada` (`id_solicitud`, `numero_respuesta`, `emitido_en`, `decision`, `justificacion`, `normativas`, `subsanacion_items`, `subsanacion_error_doc`, `subsanacion_fecha_limite`, `instrucciones_cierre`, `recursos_apelacion`, `funcionario_nombre`, `funcionario_cargo`, `codigo_verificacion`) VALUES
(1, 'RES-2026-00001', '2026-04-15 13:48:42', 'aprobado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'RES-2026-00002', '2026-04-15 13:48:24', 'aprobado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_contrato_docente`
--

CREATE TABLE `tipos_contrato_docente` (
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_contrato_docente`
--

INSERT INTO `tipos_contrato_docente` (`codigo`, `nombre`) VALUES
('catedra', 'Cátedra'),
('medio_tiempo', 'Medio tiempo'),
('otro', 'Otro'),
('tiempo_completo', 'Tiempo completo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_identificacion`
--

CREATE TABLE `tipos_identificacion` (
  `codigo` char(3) NOT NULL,
  `nombre` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_identificacion`
--

INSERT INTO `tipos_identificacion` (`codigo`, `nombre`) VALUES
('CC', 'Cédula de ciudadanía'),
('CE', 'Cédula de extranjería'),
('PAS', 'Pasaporte'),
('PPT', 'Permiso por protección temporal'),
('TI', 'Tarjeta de identidad');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_solicitud_docente`
--

CREATE TABLE `tipos_solicitud_docente` (
  `id_tipo_solicitud_docente` tinyint(3) UNSIGNED NOT NULL,
  `codigo` varchar(30) NOT NULL,
  `nombre` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_solicitud_docente`
--

INSERT INTO `tipos_solicitud_docente` (`id_tipo_solicitud_docente`, `codigo`, `nombre`) VALUES
(1, 'DOC_RECT_ACTA', 'Rectificación de Acta de Calificaciones'),
(2, 'DOC_PERM_LIC', 'Permiso Remunerado / Licencia Corta'),
(3, 'DOC_COM_EST', 'Comisión de Estudios o Servicios'),
(4, 'DOC_CERT_LAB', 'Certificado Laboral y de Ingresos'),
(5, 'DOC_MOD_CARGA', 'Modificación de Carga Académica'),
(6, 'DOC_DESC_INV', 'Solicitud de Descarga por Investigación'),
(7, 'DOC_RES_ESP', 'Reserva de Espacios de Aprendizaje'),
(8, 'DOC_MON_ASI', 'Asignación de Monitor o Asistente'),
(9, 'DOC_REPROG_EVAL', 'Reprogramación de Evaluaciones'),
(10, 'DOC_ANO_SAB', 'Solicitud de Año Sabático'),
(11, 'DOC_NOV_NOM', 'Reporte de Novedades de Nómina'),
(12, 'DOC_INS_EQUIP', 'Solicitud de Insumos o Equipos'),
(13, 'DOC_ASC_ESC', 'Postulación a Ascenso en Escalafón'),
(14, 'DOC_SAL_PED', 'Solicitud de Salida Pedagógica'),
(15, 'DOC_OTRA', 'Otra / Petición General');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_solicitud_estudiante`
--

CREATE TABLE `tipos_solicitud_estudiante` (
  `id_tipo_solicitud` tinyint(3) UNSIGNED NOT NULL,
  `codigo` varchar(30) NOT NULL,
  `nombre` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_solicitud_estudiante`
--

INSERT INTO `tipos_solicitud_estudiante` (`id_tipo_solicitud`, `codigo`, `nombre`) VALUES
(1, 'REQ_CANCEL_SEM', 'Cancelación de semestre'),
(2, 'REQ_CURSO_DIR', 'Curso dirigido'),
(3, 'REQ_CANCEL_ASIG', 'Cancelación de asignaturas'),
(4, 'REQ_CAMBIO_JORNADA', 'Cambio de jornada'),
(5, 'REQ_TRANSFER_INT', 'Transferencia interna'),
(6, 'REQ_EXAMEN_SUF', 'Examen de validación por suficiencia'),
(7, 'REQ_REINGRESO', 'Reingreso'),
(8, 'REQ_MATR_MIN', 'Matrícula mínima de créditos'),
(9, 'REQ_TRASLADO_SEDE', 'Traslado de sede'),
(10, 'REQ_PAGO_CRED', 'Pago de créditos adicionales'),
(11, 'REQ_CONST_EST', 'Constancia de estudio'),
(12, 'REQ_CERT_NOTAS', 'Certificado de notas'),
(13, 'REQ_OTRA', 'Otra');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `uk_admin_correo` (`correo`);

--
-- Indices de la tabla `categorias_docente`
--
ALTER TABLE `categorias_docente`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `decisiones_resolucion_formal`
--
ALTER TABLE `decisiones_resolucion_formal`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD PRIMARY KEY (`id_docente`),
  ADD UNIQUE KEY `uk_docentes_documento` (`documento`),
  ADD UNIQUE KEY `uk_docentes_correo` (`correo`),
  ADD KEY `idx_docentes_programa` (`id_programa`),
  ADD KEY `fk_doc_sede` (`id_sede`),
  ADD KEY `fk_doc_categoria` (`categoria_docente`),
  ADD KEY `fk_doc_contrato` (`tipo_contrato`);

--
-- Indices de la tabla `estados_solicitud`
--
ALTER TABLE `estados_solicitud`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`id_estudiante`),
  ADD UNIQUE KEY `uk_estudiantes_documento` (`documento`),
  ADD UNIQUE KEY `uk_estudiantes_correo` (`correo`),
  ADD KEY `idx_estudiantes_programa` (`id_programa`),
  ADD KEY `idx_estudiantes_sede` (`id_sede`),
  ADD KEY `fk_est_tipo_id` (`tipo_identificacion`),
  ADD KEY `fk_est_sexo` (`sexo`),
  ADD KEY `fk_est_jornada` (`id_jornada`);

--
-- Indices de la tabla `jornadas`
--
ALTER TABLE `jornadas`
  ADD PRIMARY KEY (`id_jornada`);

--
-- Indices de la tabla `motivos_solicitud_estudiante`
--
ALTER TABLE `motivos_solicitud_estudiante`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `prioridades_solicitud_docente`
--
ALTER TABLE `prioridades_solicitud_docente`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `programas`
--
ALTER TABLE `programas`
  ADD PRIMARY KEY (`id_programa`),
  ADD KEY `idx_programas_sede` (`id_sede`);

--
-- Indices de la tabla `sedes`
--
ALTER TABLE `sedes`
  ADD PRIMARY KEY (`id_sede`);

--
-- Indices de la tabla `sexos`
--
ALTER TABLE `sexos`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `idx_sol_estudiante` (`id_estudiante`),
  ADD KEY `idx_sol_docente` (`id_docente_solicitante`),
  ADD KEY `idx_sol_estado` (`estado`),
  ADD KEY `idx_sol_fecha` (`fecha_registro`),
  ADD KEY `idx_sol_tipo_est` (`id_tipo_solicitud`),
  ADD KEY `idx_sol_tipo_doc` (`id_tipo_solicitud_docente`);

--
-- Indices de la tabla `solicitud_anexos`
--
ALTER TABLE `solicitud_anexos`
  ADD PRIMARY KEY (`id_anexo`),
  ADD KEY `idx_anexos_solicitud` (`id_solicitud`);

--
-- Indices de la tabla `solicitud_detalle_docente`
--
ALTER TABLE `solicitud_detalle_docente`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `fk_sdd_prioridad` (`prioridad`);

--
-- Indices de la tabla `solicitud_detalle_estudiante`
--
ALTER TABLE `solicitud_detalle_estudiante`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `fk_sde_motivo` (`motivo`);

--
-- Indices de la tabla `solicitud_respuesta_elaborada`
--
ALTER TABLE `solicitud_respuesta_elaborada`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `fk_re_decision` (`decision`);

--
-- Indices de la tabla `tipos_contrato_docente`
--
ALTER TABLE `tipos_contrato_docente`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `tipos_identificacion`
--
ALTER TABLE `tipos_identificacion`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `tipos_solicitud_docente`
--
ALTER TABLE `tipos_solicitud_docente`
  ADD PRIMARY KEY (`id_tipo_solicitud_docente`),
  ADD UNIQUE KEY `uk_tipo_doc_codigo` (`codigo`);

--
-- Indices de la tabla `tipos_solicitud_estudiante`
--
ALTER TABLE `tipos_solicitud_estudiante`
  ADD PRIMARY KEY (`id_tipo_solicitud`),
  ADD UNIQUE KEY `uk_tipo_est_codigo` (`codigo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id_admin` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `docentes`
--
ALTER TABLE `docentes`
  MODIFY `id_docente` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id_estudiante` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id_solicitud` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitud_anexos`
--
ALTER TABLE `solicitud_anexos`
  MODIFY `id_anexo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD CONSTRAINT `fk_doc_categoria` FOREIGN KEY (`categoria_docente`) REFERENCES `categorias_docente` (`codigo`),
  ADD CONSTRAINT `fk_doc_contrato` FOREIGN KEY (`tipo_contrato`) REFERENCES `tipos_contrato_docente` (`codigo`),
  ADD CONSTRAINT `fk_doc_programa` FOREIGN KEY (`id_programa`) REFERENCES `programas` (`id_programa`),
  ADD CONSTRAINT `fk_doc_sede` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`);

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD CONSTRAINT `fk_est_jornada` FOREIGN KEY (`id_jornada`) REFERENCES `jornadas` (`id_jornada`),
  ADD CONSTRAINT `fk_est_programa` FOREIGN KEY (`id_programa`) REFERENCES `programas` (`id_programa`),
  ADD CONSTRAINT `fk_est_sede` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`),
  ADD CONSTRAINT `fk_est_sexo` FOREIGN KEY (`sexo`) REFERENCES `sexos` (`codigo`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_est_tipo_id` FOREIGN KEY (`tipo_identificacion`) REFERENCES `tipos_identificacion` (`codigo`);

--
-- Filtros para la tabla `programas`
--
ALTER TABLE `programas`
  ADD CONSTRAINT `fk_programas_sede` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`);

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `fk_sol_docente` FOREIGN KEY (`id_docente_solicitante`) REFERENCES `docentes` (`id_docente`),
  ADD CONSTRAINT `fk_sol_estado` FOREIGN KEY (`estado`) REFERENCES `estados_solicitud` (`codigo`),
  ADD CONSTRAINT `fk_sol_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id_estudiante`);

--
-- Filtros para la tabla `solicitud_anexos`
--
ALTER TABLE `solicitud_anexos`
  ADD CONSTRAINT `fk_anexos_solicitud` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitudes` (`id_solicitud`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitud_detalle_docente`
--
ALTER TABLE `solicitud_detalle_docente`
  ADD CONSTRAINT `fk_sdd_prioridad` FOREIGN KEY (`prioridad`) REFERENCES `prioridades_solicitud_docente` (`codigo`),
  ADD CONSTRAINT `fk_sdd_solicitud` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitudes` (`id_solicitud`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitud_detalle_estudiante`
--
ALTER TABLE `solicitud_detalle_estudiante`
  ADD CONSTRAINT `fk_sde_motivo` FOREIGN KEY (`motivo`) REFERENCES `motivos_solicitud_estudiante` (`codigo`),
  ADD CONSTRAINT `fk_sde_solicitud` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitudes` (`id_solicitud`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitud_respuesta_elaborada`
--
ALTER TABLE `solicitud_respuesta_elaborada`
  ADD CONSTRAINT `fk_re_decision` FOREIGN KEY (`decision`) REFERENCES `decisiones_resolucion_formal` (`codigo`),
  ADD CONSTRAINT `fk_re_solicitud` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitudes` (`id_solicitud`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
