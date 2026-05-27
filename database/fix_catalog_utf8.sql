-- Obsoleto: use database/fix_catalog_utf8.php (preserva UTF-8 correctamente en Windows).
-- Repara tildes y guiones en catálogos MySQL.
USE solicitudes_academicas;

UPDATE sedes SET nombre = 'Cúcuta' WHERE id_sede = 1;
UPDATE sedes SET nombre = 'Ocaña' WHERE id_sede = 2;

UPDATE estados_solicitud SET nombre = 'En revisión' WHERE codigo = 'en_revision';

UPDATE motivos_solicitud_estudiante SET nombre = 'Económicos' WHERE codigo = 'economicos';

UPDATE tipos_identificacion SET nombre = 'Cédula de ciudadanía' WHERE codigo = 'CC';
UPDATE tipos_identificacion SET nombre = 'Cédula de extranjería' WHERE codigo = 'CE';
UPDATE tipos_identificacion SET nombre = 'Permiso por protección temporal' WHERE codigo = 'PPT';

UPDATE tipos_contrato_docente SET nombre = 'Cátedra' WHERE codigo = 'catedra';

UPDATE decisiones_resolucion_formal SET nombre = 'Aprobado — la solicitud sigue un curso favorable' WHERE codigo = 'aprobado';
UPDATE decisiones_resolucion_formal SET nombre = 'Pendiente de información (subsanación)' WHERE codigo = 'pendiente_informacion';
UPDATE decisiones_resolucion_formal SET nombre = 'Rechazado — no cumple requisitos' WHERE codigo = 'rechazado';

UPDATE programas SET nombre = 'Técnica Profesional en Operaciones Logísticas' WHERE id_programa = 117;
UPDATE programas SET nombre = 'Tecnología en Gestión Logística Empresarial' WHERE id_programa = 118;
UPDATE programas SET nombre = 'Técnica Profesional en Producción Gráfica' WHERE id_programa = 119;
UPDATE programas SET nombre = 'Tecn. en Gestión de Contenidos Gráficos Publicitarios' WHERE id_programa = 120;
UPDATE programas SET nombre = 'Diseño Gráfico' WHERE id_programa = 121;
UPDATE programas SET nombre = 'Técnica Profesional en Soporte Informático' WHERE id_programa = 123;
UPDATE programas SET nombre = 'Tecnología en Desarrollo de Software' WHERE id_programa = 124;
UPDATE programas SET nombre = 'Ingeniería de Software' WHERE id_programa = 125;
UPDATE programas SET nombre = 'Especialización en Gestión Pública' WHERE id_programa = 126;
UPDATE programas SET nombre = 'Tecn. en Gestión de Contenidos Gráficos Public. Ocaña' WHERE id_programa = 127;
UPDATE programas SET nombre = 'Diseño Gráfico Ocaña' WHERE id_programa = 128;
UPDATE programas SET nombre = 'Tecn. en Gestión de Negocios Internacionales Ocaña' WHERE id_programa = 130;
UPDATE programas SET nombre = 'Administración de Negocios Internacionales Ocaña' WHERE id_programa = 131;
UPDATE programas SET nombre = 'Técnica Prof. en Operaciones Turísticas Virtual' WHERE id_programa = 133;
UPDATE programas SET nombre = 'Tecnología en Gestión del Turismo Sostenible Virtual' WHERE id_programa = 134;
UPDATE programas SET nombre = 'Tecnología en Gestión del Turismo Sostenible Presencial' WHERE id_programa = 137;
UPDATE programas SET nombre = 'Técnica Profesional en Procesos Contables Presencial' WHERE id_programa = 143;
UPDATE programas SET nombre = 'Administración de Negocios Internacionales Presencial' WHERE id_programa = 153;
UPDATE programas SET nombre = 'Profesional en Diseño y Administración de Negocios de la Moda' WHERE id_programa = 159;
UPDATE programas SET nombre = 'Especialización en Analítica de Datos para los Negocios Virtual' WHERE id_programa = 166;
UPDATE programas SET nombre = 'Profesional en Diseño Gráfico' WHERE id_programa = 176;
UPDATE programas SET nombre = 'Especialización en Marketing Digital Estratégico Presencial' WHERE id_programa = 177;

UPDATE tipos_solicitud_estudiante SET nombre = 'Cancelación de semestre' WHERE id_tipo_solicitud = 1;
UPDATE tipos_solicitud_estudiante SET nombre = 'Cancelación de asignaturas' WHERE id_tipo_solicitud = 3;
UPDATE tipos_solicitud_estudiante SET nombre = 'Examen de validación por suficiencia' WHERE id_tipo_solicitud = 6;
UPDATE tipos_solicitud_estudiante SET nombre = 'Matrícula mínima de créditos' WHERE id_tipo_solicitud = 8;
UPDATE tipos_solicitud_estudiante SET nombre = 'Pago de créditos adicionales' WHERE id_tipo_solicitud = 10;

UPDATE tipos_solicitud_docente SET nombre = 'Rectificación de Acta de Calificaciones' WHERE id_tipo_solicitud_docente = 1;
UPDATE tipos_solicitud_docente SET nombre = 'Comisión de Estudios o Servicios' WHERE id_tipo_solicitud_docente = 3;
UPDATE tipos_solicitud_docente SET nombre = 'Modificación de Carga Académica' WHERE id_tipo_solicitud_docente = 5;
UPDATE tipos_solicitud_docente SET nombre = 'Solicitud de Descarga por Investigación' WHERE id_tipo_solicitud_docente = 6;
UPDATE tipos_solicitud_docente SET nombre = 'Asignación de Monitor o Asistente' WHERE id_tipo_solicitud_docente = 8;
UPDATE tipos_solicitud_docente SET nombre = 'Reprogramación de Evaluaciones' WHERE id_tipo_solicitud_docente = 9;
UPDATE tipos_solicitud_docente SET nombre = 'Solicitud de Año Sabático' WHERE id_tipo_solicitud_docente = 10;
UPDATE tipos_solicitud_docente SET nombre = 'Reporte de Novedades de Nómina' WHERE id_tipo_solicitud_docente = 11;
UPDATE tipos_solicitud_docente SET nombre = 'Postulación a Ascenso en Escalafón' WHERE id_tipo_solicitud_docente = 13;
UPDATE tipos_solicitud_docente SET nombre = 'Solicitud de Salida Pedagógica' WHERE id_tipo_solicitud_docente = 14;
UPDATE tipos_solicitud_docente SET nombre = 'Otra / Petición General' WHERE id_tipo_solicitud_docente = 15;
