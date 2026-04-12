<?php
declare(strict_types=1);

/** @return array<int, array{id:int,codigo:string,nombre:string,id_sede:int}> id_sede: 1 Cúcuta, 2 Ocaña */
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

/** @return array<int, array{id:int,nombre:string}> */
function diccionario_tipos_solicitud(): array
{
    return [
        ['id' => 1, 'nombre' => 'Cancelación de semestre'],
        ['id' => 2, 'nombre' => 'Curso dirigido'],
        ['id' => 3, 'nombre' => 'Cancelación de asignaturas'],
        ['id' => 4, 'nombre' => 'Cambio de jornada'],
        ['id' => 5, 'nombre' => 'Transferencia interna'],
        ['id' => 6, 'nombre' => 'Examen de validación por suficiencia'],
        ['id' => 7, 'nombre' => 'Reingreso'],
        ['id' => 8, 'nombre' => 'Matrícula mínima de créditos'],
        ['id' => 9, 'nombre' => 'Traslado de sede'],
        ['id' => 10, 'nombre' => 'Pago de créditos adicionales'],
        ['id' => 11, 'nombre' => 'Constancia de estudio'],
        ['id' => 12, 'nombre' => 'Certificado de notas'],
        ['id' => 13, 'nombre' => 'Otra'],
    ];
}

/** @return array<int, array{id:int,nombre:string}> */
function diccionario_sedes(): array
{
    return [
        ['id' => 1, 'nombre' => 'Cúcuta'],
        ['id' => 2, 'nombre' => 'Ocaña'],
    ];
}

/** @return array<int, array{id:int,nombre:string}> */
function diccionario_jornadas(): array
{
    return [
        ['id' => 1, 'nombre' => 'Diurna'],
        ['id' => 2, 'nombre' => 'Nocturna'],
        ['id' => 3, 'nombre' => 'Distancia'],
        ['id' => 4, 'nombre' => 'Virtual'],
    ];
}

/** @return list<array{codigo:string,nombre:string}> */
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

/** @return list<array{codigo:string,nombre:string}> */
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

/** Sede del programa según diccionario: 1 Cúcuta, 2 Ocaña. */
function programa_id_sede(int $idPrograma): int
{
    foreach (diccionario_programas() as $p) {
        if ((int) $p['id'] === $idPrograma) {
            return (int) ($p['id_sede'] ?? 1);
        }
    }
    return 1;
}

/**
 * Sede del docente: campo explícito o inferida por la carrera (registros antiguos).
 *
 * @param array<string, mixed> $docente
 */
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

function tipo_solicitud_nombre(int $id): string
{
    foreach (diccionario_tipos_solicitud() as $t) {
        if ((int) $t['id'] === $id) {
            return $t['nombre'];
        }
    }
    return 'Tipo #' . $id;
}
