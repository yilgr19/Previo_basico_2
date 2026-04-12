<?php
declare(strict_types=1);

/** @return array<int, array{id:int,codigo:string,nombre:string}> */
function diccionario_programas(): array
{
    return [
        ['id' => 117, 'codigo' => '90604', 'nombre' => 'Técnica Profesional en Operaciones Logísticas'],
        ['id' => 118, 'codigo' => '90605', 'nombre' => 'Tecnología en Gestión Logística Empresarial'],
        ['id' => 119, 'codigo' => '91390', 'nombre' => 'Técnica Profesional en Producción Gráfica'],
        ['id' => 120, 'codigo' => '107156', 'nombre' => 'Tecn. en Gestión de Contenidos Gráficos Publicitarios'],
        ['id' => 121, 'codigo' => '91388', 'nombre' => 'Diseño Gráfico'],
        ['id' => 123, 'codigo' => '107859', 'nombre' => 'Técnica Profesional en Soporte Informático'],
        ['id' => 124, 'codigo' => '107860', 'nombre' => 'Tecnología en Desarrollo de Software'],
        ['id' => 125, 'codigo' => '107861', 'nombre' => 'Ingeniería de Software'],
        ['id' => 126, 'codigo' => '107858', 'nombre' => 'Especialización en Gestión Pública'],
        ['id' => 127, 'codigo' => '108788', 'nombre' => 'Tecn. en Gestión de Contenidos Gráficos Public. Ocaña'],
        ['id' => 128, 'codigo' => '102041', 'nombre' => 'Diseño Gráfico Ocaña'],
        ['id' => 130, 'codigo' => '102517', 'nombre' => 'Tecn. en Gestión de Negocios Internacionales Ocaña'],
        ['id' => 131, 'codigo' => '102518', 'nombre' => 'Administración de Negocios Internacionales Ocaña'],
        ['id' => 133, 'codigo' => '102887', 'nombre' => 'Técnica Prof. en Operaciones Turísticas Virtual'],
        ['id' => 134, 'codigo' => '111410', 'nombre' => 'Tecnología en Gestión del Turismo Sostenible Virtual'],
        ['id' => 137, 'codigo' => '111412', 'nombre' => 'Tecnología en Gestión del Turismo Sostenible Presencial'],
        ['id' => 143, 'codigo' => '54348', 'nombre' => 'Técnica Profesional en Procesos Contables Presencial'],
        ['id' => 153, 'codigo' => '116880', 'nombre' => 'Administración de Negocios Internacionales Presencial'],
        ['id' => 159, 'codigo' => '104671', 'nombre' => 'Profesional en Diseño y Administración de Negocios de la Moda'],
        ['id' => 166, 'codigo' => '117775', 'nombre' => 'Especialización en Analítica de Datos para los Negocios Virtual'],
        ['id' => 176, 'codigo' => '91388', 'nombre' => 'Profesional en Diseño Gráfico'],
        ['id' => 177, 'codigo' => '118275', 'nombre' => 'Especialización en Marketing Digital Estratégico Presencial'],
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

function programa_label_by_id(int $id): string
{
    foreach (diccionario_programas() as $p) {
        if ((int) $p['id'] === $id) {
            return '[' . $p['codigo'] . '] ' . $p['nombre'];
        }
    }
    return 'Programa ID ' . $id;
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
