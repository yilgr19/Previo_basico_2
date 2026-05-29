<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class PlazosSolicitudService
{
    private static bool $tablaLista = false;

    public static function plazoDias(int $idTipo, int $idSede): ?int
    {
        if ($idTipo <= 0 || $idSede <= 0) {
            return null;
        }
        if (!function_exists('storage_usa_mysql') || !storage_usa_mysql()) {
            return self::plazoDiasDesdeCatalogoTexto($idTipo);
        }

        self::asegurarTabla();

        $st = Database::pdo()->prepare(
            'SELECT plazo FROM tipo_solicitud_plazo_sede
             WHERE id_tipo_solicitud = ? AND id_sede = ? LIMIT 1'
        );
        $st->execute([$idTipo, $idSede]);
        $row = $st->fetch();
        if ($row !== false && isset($row['plazo'])) {
            return (int) $row['plazo'];
        }

        return self::plazoDiasDesdeCatalogoTexto($idTipo);
    }

    public static function plazoTexto(int $idTipo, int $idSede): string
    {
        $dias = self::plazoDias($idTipo, $idSede);
        if ($dias === null || $dias <= 0) {
            return '';
        }

        return $dias === 1 ? '1 día' : $dias . ' días';
    }

    /**
     * Semáforo de plazo: verde (a tiempo), amarillo (≤3 días), rojo (vencida).
     *
     * @return array{
     *   activo: bool,
     *   estado: 'verde'|'amarillo'|'rojo'|'gris'|'ninguno',
     *   dias_restantes: ?int,
     *   fecha_vencimiento: ?string,
     *   etiqueta: string
     * }
     */
    public static function semaforoDesdeRegistro(
        int $idTipo,
        int $idSede,
        string $fechaRegistro,
        int $umbralAmarillo = 3
    ): array {
        $gris = [
            'activo' => false,
            'estado' => 'gris',
            'dias_restantes' => null,
            'fecha_vencimiento' => null,
            'etiqueta' => '',
        ];

        $diasPlazo = self::plazoDias($idTipo, $idSede);
        $fechaRegistro = trim($fechaRegistro);
        if ($diasPlazo === null || $diasPlazo <= 0 || $fechaRegistro === '') {
            return $gris;
        }

        $tz = new \DateTimeZone(defined('APP_TIMEZONE') ? APP_TIMEZONE : 'America/Bogota');
        $inicio = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $fechaRegistro, $tz);
        if ($inicio === false) {
            $inicio = \DateTimeImmutable::createFromFormat('Y-m-d', substr($fechaRegistro, 0, 10), $tz);
        }
        if ($inicio === false) {
            return $gris;
        }

        $vence = $inicio->modify('+' . $diasPlazo . ' days')->setTime(0, 0, 0);
        $hoy = new \DateTimeImmutable('today', $tz);
        $diasRestantes = (int) $hoy->diff($vence)->format('%r%a');

        $fechaVence = $vence->format('Y-m-d');

        if ($diasRestantes < 0) {
            $hace = abs($diasRestantes);

            return [
                'activo' => true,
                'estado' => 'rojo',
                'dias_restantes' => $diasRestantes,
                'fecha_vencimiento' => $fechaVence,
                'etiqueta' => $hace === 1 ? 'Vencida (hace 1 día)' : 'Vencida (hace ' . $hace . ' días)',
            ];
        }

        if ($diasRestantes <= $umbralAmarillo) {
            return [
                'activo' => true,
                'estado' => 'amarillo',
                'dias_restantes' => $diasRestantes,
                'fecha_vencimiento' => $fechaVence,
                'etiqueta' => $diasRestantes === 0
                    ? 'Vence hoy'
                    : ($diasRestantes === 1 ? 'Vence en 1 día' : 'Vence en ' . $diasRestantes . ' días'),
            ];
        }

        return [
            'activo' => true,
            'estado' => 'verde',
            'dias_restantes' => $diasRestantes,
            'fecha_vencimiento' => $fechaVence,
            'etiqueta' => $diasRestantes === 1 ? 'A tiempo (1 día restante)' : 'A tiempo (' . $diasRestantes . ' días)',
        ];
    }

    /** @return array{activo: bool, estado: string, dias_restantes: ?int, fecha_vencimiento: ?string, etiqueta: string} */
    public static function semaforoSolicitud(
        array $solicitud,
        ?array $estudiante = null,
        ?array $docenteSolicitante = null
    ): array {
        $sinSemaforo = [
            'activo' => false,
            'estado' => 'ninguno',
            'dias_restantes' => null,
            'fecha_vencimiento' => null,
            'etiqueta' => '',
        ];

        if (function_exists('solicitud_pendiente_de_respuesta_institucional')
            && !solicitud_pendiente_de_respuesta_institucional($solicitud)) {
            return $sinSemaforo;
        }

        $idTipo = (int) ($solicitud['id_tipo_solicitud'] ?? 0);
        if ($idTipo <= 0) {
            $idTipo = (int) ($solicitud['id_tipo_solicitud_docente'] ?? 0);
        }

        $idSede = function_exists('solicitud_sede_para_bandera_gestion')
            ? solicitud_sede_para_bandera_gestion($solicitud, $estudiante, $docenteSolicitante)
            : 1;

        return self::semaforoDesdeRegistro(
            $idTipo,
            $idSede,
            (string) ($solicitud['fecha_registro'] ?? '')
        );
    }

    /** @return array<string, int> Claves "idTipo-idSede" => días */
    public static function matrizParaFrontend(): array
    {
        if (!function_exists('storage_usa_mysql') || !storage_usa_mysql()) {
            return [];
        }

        self::asegurarTabla();

        $out = [];
        $rows = Database::pdo()->query(
            'SELECT id_tipo_solicitud, id_sede, plazo FROM tipo_solicitud_plazo_sede'
        )->fetchAll();
        foreach ($rows as $r) {
            $k = (int) $r['id_tipo_solicitud'] . '-' . (int) $r['id_sede'];
            $out[$k] = (int) $r['plazo'];
        }

        return $out;
    }

    /** @return list<array{id_tipo_solicitud: int, id_sede: int, plazo: int, tipo_nombre: string, sede_nombre: string, tipo_codigo: string}> */
    public static function listarConfigurados(): array
    {
        if (!function_exists('storage_usa_mysql') || !storage_usa_mysql()) {
            return [];
        }

        self::asegurarTabla();

        $sql = 'SELECT p.id_tipo_solicitud, p.id_sede, p.plazo,
                       t.nombre AS tipo_nombre, t.codigo AS tipo_codigo, s.nombre AS sede_nombre
                FROM tipo_solicitud_plazo_sede p
                INNER JOIN tipos_solicitud_estudiante t ON t.id_tipo_solicitud = p.id_tipo_solicitud
                INNER JOIN sedes s ON s.id_sede = p.id_sede
                ORDER BY t.id_tipo_solicitud, p.id_sede';

        $rows = Database::pdo()->query($sql)->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id_tipo_solicitud' => (int) $r['id_tipo_solicitud'],
                'id_sede' => (int) $r['id_sede'],
                'plazo' => (int) $r['plazo'],
                'tipo_nombre' => (string) $r['tipo_nombre'],
                'tipo_codigo' => (string) $r['tipo_codigo'],
                'sede_nombre' => (string) $r['sede_nombre'],
            ];
        }

        return $out;
    }

    /**
     * Todas las combinaciones tipo × sede, con plazo si existe en BD.
     *
     * @return list<array{id_tipo_solicitud: int, id_sede: int, plazo: ?int, tipo_nombre: string, sede_nombre: string, tipo_codigo: string}>
     */
    public static function listarMatrizCompleta(): array
    {
        $porClave = [];
        foreach (self::listarConfigurados() as $p) {
            $porClave[(int) $p['id_tipo_solicitud'] . '-' . (int) $p['id_sede']] = $p;
        }

        $out = [];
        foreach (diccionario_tipos_solicitud() as $t) {
            $idTipo = (int) ($t['id'] ?? 0);
            if ($idTipo <= 0) {
                continue;
            }
            foreach (diccionario_sedes() as $s) {
                $idSede = (int) ($s['id'] ?? 0);
                if ($idSede <= 0) {
                    continue;
                }
                $clave = $idTipo . '-' . $idSede;
                if (isset($porClave[$clave])) {
                    $out[] = $porClave[$clave];
                    continue;
                }
                $out[] = [
                    'id_tipo_solicitud' => $idTipo,
                    'id_sede' => $idSede,
                    'plazo' => null,
                    'tipo_nombre' => (string) ($t['nombre'] ?? ''),
                    'tipo_codigo' => (string) ($t['codigo'] ?? ''),
                    'sede_nombre' => (string) ($s['nombre'] ?? ''),
                ];
            }
        }

        return $out;
    }

    /** @return array{0: string, 1: string} */
    public static function guardarMasivoDesdePost(): array
    {
        $plazo = (int) (post('plazo_masivo', '0') ?? '0');
        $aplicarTodos = (post('aplicar_todos', '') ?? '') === '1';

        if ($plazo < 1 || $plazo > 999) {
            return ['Indique un plazo entre 1 y 999 días para la asignación masiva.', 'warning'];
        }

        $pares = [];
        if ($aplicarTodos) {
            foreach (diccionario_tipos_solicitud() as $t) {
                $idTipo = (int) ($t['id'] ?? 0);
                if ($idTipo <= 0) {
                    continue;
                }
                foreach (diccionario_sedes() as $s) {
                    $idSede = (int) ($s['id'] ?? 0);
                    if ($idSede <= 0 || !in_array($idSede, [1, 2], true)) {
                        continue;
                    }
                    $pares[] = [$idTipo, $idSede];
                }
            }
        } else {
            $seleccion = $_POST['seleccion'] ?? [];
            if (!is_array($seleccion)) {
                $seleccion = $seleccion !== '' ? [(string) $seleccion] : [];
            }
            foreach ($seleccion as $key) {
                if (!preg_match('/^(\d+)-(\d+)$/', (string) $key, $m)) {
                    continue;
                }
                $idTipo = (int) $m[1];
                $idSede = (int) $m[2];
                if ($idTipo <= 0 || !tipo_solicitud_por_id($idTipo)) {
                    continue;
                }
                if (!in_array($idSede, [1, 2], true)) {
                    continue;
                }
                $pares[] = [$idTipo, $idSede];
            }
        }

        if ($pares === []) {
            return ['Seleccione al menos una fila o use «Aplicar a todos».', 'warning'];
        }

        if (!function_exists('storage_usa_mysql') || !storage_usa_mysql()) {
            return ['La configuración de plazos requiere MySQL activo.', 'warning'];
        }

        self::asegurarTabla();

        $textoPlazo = $plazo === 1 ? '1 día' : $plazo . ' días';
        $pdo = Database::pdo();
        $ins = $pdo->prepare(
            'INSERT INTO tipo_solicitud_plazo_sede (id_tipo_solicitud, id_sede, plazo)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE plazo = VALUES(plazo)'
        );
        $updTipo = $pdo->prepare(
            'UPDATE tipos_solicitud_estudiante SET plazo = ? WHERE id_tipo_solicitud = ?'
        );

        $tiposTocados = [];
        $pdo->beginTransaction();
        try {
            foreach ($pares as [$idTipo, $idSede]) {
                $ins->execute([$idTipo, $idSede, $plazo]);
                $tiposTocados[$idTipo] = true;
            }
            foreach (array_keys($tiposTocados) as $idTipo) {
                $updTipo->execute([$textoPlazo, $idTipo]);
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }

        $n = count($pares);
        $etiqueta = $aplicarTodos ? 'todas las combinaciones tipo y sede' : $n . ' combinación' . ($n === 1 ? '' : 'es');

        return ['Plazo de ' . $textoPlazo . ' aplicado a ' . $etiqueta . '.', 'success'];
    }

    /** @return array{0: string, 1: string} */
    public static function guardarDesdePost(): array
    {
        $idTipo = (int) post('id_tipo_solicitud', '0');
        $idSede = (int) post('id_sede', '0');
        $plazo = (int) post('plazo', '0');

        if ($idTipo <= 0) {
            return ['Seleccione el tipo de solicitud.', 'warning'];
        }
        if ($idSede <= 0 || !in_array($idSede, [1, 2], true)) {
            return ['Seleccione la sede (Cúcuta u Ocaña).', 'warning'];
        }
        if ($plazo < 1 || $plazo > 999) {
            return ['Indique un plazo entre 1 y 999 días.', 'warning'];
        }

        if (!tipo_solicitud_por_id($idTipo)) {
            return ['Tipo de solicitud no válido.', 'warning'];
        }

        if (!function_exists('storage_usa_mysql') || !storage_usa_mysql()) {
            return ['La configuración de plazos requiere MySQL activo.', 'warning'];
        }

        self::asegurarTabla();

        $pdo = Database::pdo();
        $pdo->prepare(
            'INSERT INTO tipo_solicitud_plazo_sede (id_tipo_solicitud, id_sede, plazo)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE plazo = VALUES(plazo)'
        )->execute([$idTipo, $idSede, $plazo]);

        $textoPlazo = $plazo === 1 ? '1 día' : $plazo . ' días';
        $pdo->prepare(
            'UPDATE tipos_solicitud_estudiante SET plazo = ? WHERE id_tipo_solicitud = ?'
        )->execute([$textoPlazo, $idTipo]);

        return ['Plazo guardado: ' . tipo_solicitud_nombre($idTipo) . ' — ' . sede_nombre($idSede) . ' (' . $textoPlazo . ').', 'success'];
    }

    /** @return array{0: string, 1: string} */
    public static function eliminarDesdePost(): array
    {
        $idTipo = (int) post('id_tipo_solicitud', '0');
        $idSede = (int) post('id_sede', '0');
        if ($idTipo <= 0 || $idSede <= 0) {
            return ['Registro no válido.', 'warning'];
        }

        self::asegurarTabla();

        Database::pdo()->prepare(
            'DELETE FROM tipo_solicitud_plazo_sede WHERE id_tipo_solicitud = ? AND id_sede = ?'
        )->execute([$idTipo, $idSede]);

        return ['Plazo eliminado para ese tipo y sede.', 'success'];
    }

    /** Crea la tabla si aún no existe (primera visita a Plazos sin importar el SQL manual). */
    public static function asegurarTabla(): void
    {
        if (self::$tablaLista) {
            return;
        }
        if (!function_exists('storage_usa_mysql') || !storage_usa_mysql()) {
            return;
        }

        $pdo = Database::pdo();
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS tipo_solicitud_plazo_sede (
                id_tipo_solicitud tinyint(3) UNSIGNED NOT NULL,
                id_sede tinyint(3) UNSIGNED NOT NULL,
                plazo smallint(5) UNSIGNED NOT NULL COMMENT \'Plazo en días\',
                PRIMARY KEY (id_tipo_solicitud, id_sede),
                KEY fk_tps_sede (id_sede),
                CONSTRAINT fk_tps_tipo FOREIGN KEY (id_tipo_solicitud)
                    REFERENCES tipos_solicitud_estudiante (id_tipo_solicitud)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_tps_sede_ref FOREIGN KEY (id_sede)
                    REFERENCES sedes (id_sede)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        self::$tablaLista = true;
    }

    private static function plazoDiasDesdeCatalogoTexto(int $idTipo): ?int
    {
        $t = tipo_solicitud_por_id($idTipo);
        if (!$t) {
            return null;
        }
        $txt = (string) ($t['plazo'] ?? '');
        if (preg_match('/(\d+)/', $txt, $m) === 1) {
            return (int) $m[1];
        }

        return null;
    }
}
