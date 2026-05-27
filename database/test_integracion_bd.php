<?php
declare(strict_types=1);

/**
 * Prueba exhaustiva de persistencia MySQL (misma capa que la aplicación web).
 *
 * CLI:
 *   php database/test_integracion_bd.php          → ejecuta pruebas y elimina datos de prueba
 *   php database/test_integracion_bd.php --keep   → deja los registros creados
 */
require_once dirname(__DIR__) . '/config/config.php';

use App\Core\Database;
use App\Services\GestionAcademicaService;
use App\Services\SolicitudesAnexosUpload;
use App\Services\SolicitudesService;

final class TestIntegracionBd
{
    private \PDO $pdo;

    /** @var list<array{name: string, ok: bool, detail: string}> */
    private array $results = [];

    private int $passed = 0;

    private int $failed = 0;

    private bool $keepData;

    private string $suffix;

    private int $idEstudiante = 0;

    private int $idDocente = 0;

    private int $idSolEst = 0;

    private int $idSolDoc = 0;

    public function __construct(bool $keepData)
    {
        $this->keepData = $keepData;
        $this->suffix = (string) time();
    }

    public function run(): int
    {
        $this->banner();

        if (!defined('DB_ENABLED') || DB_ENABLED !== true) {
            $this->fail('config', 'DB_ENABLED debe ser true para esta prueba.');
            $this->report();

            return 1;
        }

        try {
            $this->pdo = Database::pdo();
            $this->pdo->exec('SET NAMES utf8mb4');
            $this->ok('conexion', 'MySQL ' . DB_NAME . '@' . DB_HOST);
        } catch (\Throwable $e) {
            $this->fail('conexion', $e->getMessage());
            $this->report();

            return 1;
        }

        $this->testCatalogos();
        $this->testCrearEstudiante();
        $this->testCrearDocente();
        $this->testLoginUsuarios();
        $this->testSolicitudEstudiante();
        $this->testSolicitudDocente();
        $this->testRespuestaAdminBreve();
        $this->testRespuestaAdminElaborada();
        $this->testAnexoSolicitud();
        $this->testCoherenciaLoadData();
        $this->testUtf8SinInterrogacion();

        if (!$this->keepData) {
            $this->limpiarDatosPrueba();
        } else {
            $this->ok('cleanup', 'Datos de prueba conservados (--keep). IDs: est=' . $this->idEstudiante
                . ', doc=' . $this->idDocente . ', solEst=' . $this->idSolEst . ', solDoc=' . $this->idSolDoc);
        }

        $this->report();

        return $this->failed > 0 ? 1 : 0;
    }

    private function banner(): void
    {
        echo "\n";
        echo "╔══════════════════════════════════════════════════════════╗\n";
        echo "║  TEST INTEGRACIÓN BD — Sistema de solicitudes académicas  ║\n";
        echo "╚══════════════════════════════════════════════════════════╝\n\n";
    }

    private function testCatalogos(): void
    {
        $tablas = [
            'sedes', 'jornadas', 'programas', 'estados_solicitud',
            'tipos_solicitud_estudiante', 'tipos_solicitud_docente',
            'motivos_solicitud_estudiante', 'decisiones_resolucion_formal',
        ];
        foreach ($tablas as $t) {
            $n = (int) $this->pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
            if ($n > 0) {
                $this->ok("catalogo:$t", "$n registros");
            } else {
                $this->fail("catalogo:$t", 'Tabla vacía — importe solicitudes_academicas.sql');
            }
        }
    }

    private function testCrearEstudiante(): void
    {
        $_POST = [
            'tipo_identificacion' => 'CC',
            'documento' => 'TST-E-' . $this->suffix,
            'nombre' => 'Prueba',
            'apellido' => 'Integración',
            'correo' => 'test.est.' . $this->suffix . '@prueba.local',
            'sexo' => 'F',
            'id_programa' => '124',
            'semestre' => '3',
            'fecha_nacimiento' => '2004-06-15',
            'direccion' => 'Calle Test 123',
            'barrio' => 'Centro',
            'telefono' => '3009990001',
            'id_sede' => '1',
            'id_jornada' => '1',
            'clave' => 'test123',
            'clave_confirmar' => 'test123',
        ];
        $_SERVER['REQUEST_METHOD'] = 'POST';

        [$msg, $tipo] = GestionAcademicaService::agregarEstudiante();
        if ($tipo !== 'success') {
            $this->fail('crear_estudiante', $msg);

            return;
        }

        $st = $this->pdo->prepare('SELECT * FROM estudiantes WHERE documento = ? LIMIT 1');
        $st->execute(['TST-E-' . $this->suffix]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            $this->fail('crear_estudiante', 'No aparece en tabla estudiantes');

            return;
        }

        $this->idEstudiante = (int) $row['id_estudiante'];
        $viaRepo = repo_estudiante_por_id($this->idEstudiante);
        if (!$viaRepo || (string) $viaRepo['correo'] !== $_POST['correo']) {
            $this->fail('crear_estudiante', 'repo_estudiante_por_id no coincide');

            return;
        }

        $this->ok('crear_estudiante', "id={$this->idEstudiante}, documento={$row['documento']}");
    }

    private function testCrearDocente(): void
    {
        $_POST = [
            'nombre' => 'Docente',
            'apellido' => 'Prueba BD',
            'documento' => 'TST-D-' . $this->suffix,
            'correo' => 'test.doc.' . $this->suffix . '@prueba.local',
            'telefono' => '3009990002',
            'id_sede' => '1',
            'id_programa' => '124',
            'clave' => 'test123',
        ];
        $_SERVER['REQUEST_METHOD'] = 'POST';

        [$msg, $tipo] = GestionAcademicaService::agregarDocente();
        if ($tipo !== 'success') {
            $this->fail('crear_docente', $msg);

            return;
        }

        $st = $this->pdo->prepare('SELECT * FROM docentes WHERE documento = ? LIMIT 1');
        $st->execute(['TST-D-' . $this->suffix]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            $this->fail('crear_docente', 'No aparece en tabla docentes');

            return;
        }

        $this->idDocente = (int) $row['id_docente'];
        $this->ok('crear_docente', "id={$this->idDocente}, programa={$row['programa']}");
    }

    private function testLoginUsuarios(): void
    {
        if ($this->idEstudiante <= 0 || $this->idDocente <= 0) {
            $this->fail('login', 'Omitido: faltan usuarios de prueba');

            return;
        }

        $_SESSION = [];
        $okEst = attempt_login('TST-E-' . $this->suffix, 'test123');
        $rolEst = (string) ($_SESSION['user']['rol'] ?? '');
        $_SESSION = [];
        $okDoc = attempt_login('TST-D-' . $this->suffix, 'test123');
        $rolDoc = (string) ($_SESSION['user']['rol'] ?? '');

        if ($okEst && $rolEst === ROLE_ESTUDIANTE && $okDoc && $rolDoc === ROLE_DOCENTE) {
            $this->ok('login', 'Estudiante y docente autentican contra MySQL');
        } else {
            $this->fail('login', "est=$okEst/$rolEst doc=$okDoc/$rolDoc");
        }
    }

    private function testSolicitudEstudiante(): void
    {
        if ($this->idEstudiante <= 0) {
            $this->fail('solicitud_estudiante', 'Omitido: sin estudiante');

            return;
        }

        $_POST = [
            'id_tipo_solicitud' => '2',
            'periodo_academico' => '2026-1',
            'id_sede_solicitud' => '1',
            'id_jornada_solicitud' => '1',
            'motivo_solicitud' => 'economicos',
            'exposicion' => 'Solicitud de prueba automática de integración con base de datos MySQL.',
            'consentimiento_veracidad' => '1',
        ];
        $_FILES = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';

        [$msg, $tipo] = SolicitudesService::registrarDesdeEstudiante($this->idEstudiante);
        if ($tipo !== 'success') {
            $this->fail('solicitud_estudiante', $msg);

            return;
        }

        $st = $this->pdo->prepare(
            'SELECT s.id_solicitud, s.estado, de.exposicion, de.motivo
             FROM solicitudes s
             INNER JOIN solicitud_detalle_estudiante de ON de.id_solicitud = s.id_solicitud
             WHERE s.id_estudiante = ? ORDER BY s.id_solicitud DESC LIMIT 1'
        );
        $st->execute([$this->idEstudiante]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if (!$row || ($row['motivo'] ?? '') !== 'economicos') {
            $this->fail('solicitud_estudiante', 'Falta fila en solicitudes o solicitud_detalle_estudiante');

            return;
        }

        $this->idSolEst = (int) $row['id_solicitud'];
        $this->ok('solicitud_estudiante', "id={$this->idSolEst}, estado={$row['estado']}");
    }

    private function testSolicitudDocente(): void
    {
        if ($this->idDocente <= 0) {
            $this->fail('solicitud_docente', 'Omitido: sin docente');

            return;
        }

        $_POST = [
            'id_tipo_solicitud_docente' => '3',
            'asunto' => 'Comisión prueba BD',
            'prioridad' => 'media',
            'nrc' => '99999',
            'nombre_materia' => 'Integración de sistemas',
            'horario_impactado' => 'Lunes 8-10',
            'plan_contingencia' => 'Clase virtual de respaldo',
            'descripcion_detallada' => 'Descripción de prueba para validar persistencia docente en MySQL.',
            'sustento_legal' => 'Acuerdo 001/2026',
            'fecha_inicio' => '2026-06-01',
            'fecha_fin' => '2026-06-15',
            'consentimiento_responsabilidad' => '1',
        ];
        $_FILES = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';

        [$msg, $tipo] = SolicitudesService::registrarDesdeDocente($this->idDocente);
        if ($tipo !== 'success') {
            $this->fail('solicitud_docente', $msg);

            return;
        }

        $st = $this->pdo->prepare(
            'SELECT s.id_solicitud, dd.asunto, dd.prioridad
             FROM solicitudes s
             INNER JOIN solicitud_detalle_docente dd ON dd.id_solicitud = s.id_solicitud
             WHERE s.id_docente_solicitante = ? ORDER BY s.id_solicitud DESC LIMIT 1'
        );
        $st->execute([$this->idDocente]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if (!$row || ($row['prioridad'] ?? '') !== 'media') {
            $this->fail('solicitud_docente', 'Falta fila en solicitudes o solicitud_detalle_docente');

            return;
        }

        $this->idSolDoc = (int) $row['id_solicitud'];
        $this->ok('solicitud_docente', "id={$this->idSolDoc}, asunto={$row['asunto']}");
    }

    private function testRespuestaAdminBreve(): void
    {
        if ($this->idSolEst <= 0) {
            $this->fail('respuesta_breve', 'Omitido: sin solicitud estudiante');

            return;
        }

        [$msg, $tipo] = SolicitudesService::actualizarEstadoAdmin(
            $this->idSolEst,
            'en_revision',
            'Respuesta breve de prueba — integración BD.',
            false
        );
        if ($tipo !== 'success') {
            $this->fail('respuesta_breve', $msg);

            return;
        }

        $st = $this->pdo->prepare(
            'SELECT estado, respuesta, respondido_en FROM solicitudes WHERE id_solicitud = ?'
        );
        $st->execute([$this->idSolEst]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if (!$row || ($row['estado'] ?? '') !== 'en_revision' || trim((string) ($row['respuesta'] ?? '')) === '') {
            $this->fail('respuesta_breve', 'respuesta o respondido_en no persistidos');

            return;
        }

        $this->ok('respuesta_breve', "estado={$row['estado']}, respondido={$row['respondido_en']}");
    }

    private function testRespuestaAdminElaborada(): void
    {
        if ($this->idSolDoc <= 0) {
            $this->fail('respuesta_elaborada', 'Omitido: sin solicitud docente');

            return;
        }

        $_POST = [
            'elab_decision' => 'aprobado',
            'elab_justificacion' => 'Justificación de prueba — cumple requisitos del reglamento.',
            'elab_normativas' => 'Art. 12 reglamento docente',
            'elab_funcionario_nombre' => 'Admin Prueba',
            'elab_funcionario_cargo' => 'Coordinación académica',
        ];
        $_SERVER['REQUEST_METHOD'] = 'POST';

        [$msg, $tipo] = SolicitudesService::actualizarEstadoAdmin(
            $this->idSolDoc,
            'aprobada',
            'Trámite aprobado en prueba de integración.',
            true
        );
        if ($tipo !== 'success') {
            $this->fail('respuesta_elaborada', $msg);

            return;
        }

        $st = $this->pdo->prepare(
            'SELECT s.estado, s.respuesta, re.numero_respuesta, re.decision, re.justificacion
             FROM solicitudes s
             LEFT JOIN solicitud_respuesta_elaborada re ON re.id_solicitud = s.id_solicitud
             WHERE s.id_solicitud = ?'
        );
        $st->execute([$this->idSolDoc]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if (!$row || ($row['decision'] ?? '') !== 'aprobado' || trim((string) ($row['justificacion'] ?? '')) === '') {
            $this->fail('respuesta_elaborada', 'Falta fila en solicitud_respuesta_elaborada');

            return;
        }

        $this->ok('respuesta_elaborada', "{$row['numero_respuesta']} | {$row['decision']}");
    }

    private function testAnexoSolicitud(): void
    {
        if ($this->idSolEst <= 0) {
            $this->fail('anexo', 'Omitido: sin solicitud');

            return;
        }

        // Persistencia en BD (metadatos + disco); la subida HTTP usa is_uploaded_file() solo en la web.
        $nombreFs = 'ev_test_' . $this->suffix . '.pdf';
        $dir = SolicitudesAnexosUpload::directorioSolicitud($this->idSolEst);
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            $this->fail('anexo', 'No se pudo crear carpeta uploads');

            return;
        }
        file_put_contents($dir . DIRECTORY_SEPARATOR . $nombreFs, '%PDF-1.4 test integracion');

        $anexo = [
            'guardado' => $nombreFs,
            'original' => 'evidencia_prueba.pdf',
            'mime' => 'application/pdf',
            'bytes' => (int) filesize($dir . DIRECTORY_SEPARATOR . $nombreFs),
            'categoria' => 'general',
        ];

        $rows = load_data('solicitudes');
        foreach ($rows as &$s) {
            if ((int) ($s['id_solicitud'] ?? 0) === $this->idSolEst) {
                $existentes = is_array($s['anexos_archivos'] ?? null) ? $s['anexos_archivos'] : [];
                $s['anexos_archivos'] = array_merge($existentes, [$anexo]);
                break;
            }
        }
        unset($s);
        if (!save_data('solicitudes', $rows)) {
            $this->fail('anexo', 'save_data falló');

            return;
        }

        $st = $this->pdo->prepare(
            'SELECT guardado, original, categoria FROM solicitud_anexos WHERE id_solicitud = ? AND guardado = ?'
        );
        $st->execute([$this->idSolEst, $nombreFs]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if (!$row || ($row['categoria'] ?? '') !== 'general') {
            $this->fail('anexo', 'solicitud_anexos sin fila esperada');

            return;
        }

        $this->ok('anexo', "MySQL + disco: {$row['original']} ({$row['guardado']})");
    }

    private function testCoherenciaLoadData(): void
    {
        if ($this->idSolEst <= 0) {
            $this->fail('coherencia', 'Omitido');

            return;
        }

        $viaApp = null;
        foreach (load_data('solicitudes') as $s) {
            if ((int) ($s['id_solicitud'] ?? 0) === $this->idSolEst) {
                $viaApp = $s;
                break;
            }
        }
        if ($viaApp === null) {
            $this->fail('coherencia', 'load_data no devuelve solicitud de prueba');

            return;
        }

        $checks = [
            'estado' => 'en_revision',
            'detalle_estudiante' => is_array($viaApp['detalle_estudiante'] ?? null),
            'anexos' => is_array($viaApp['anexos_archivos'] ?? null) && count($viaApp['anexos_archivos']) > 0,
        ];
        $bad = [];
        if (($viaApp['estado'] ?? '') !== $checks['estado']) {
            $bad[] = 'estado';
        }
        if (!$checks['detalle_estudiante']) {
            $bad[] = 'detalle_estudiante';
        }
        if (!$checks['anexos']) {
            $bad[] = 'anexos_archivos';
        }

        if ($bad !== []) {
            $this->fail('coherencia', 'load_data incompleto: ' . implode(', ', $bad));

            return;
        }

        $this->ok('coherencia', 'load_data() reconstruye solicitud + detalle + anexos desde MySQL');
    }

    private function testUtf8SinInterrogacion(): void
    {
        $ids = array_filter([$this->idEstudiante, $this->idDocente, $this->idSolEst, $this->idSolDoc]);
        if ($ids === []) {
            $this->fail('utf8', 'Omitido');

            return;
        }

        $tablas = [
            'estudiantes' => "id_estudiante = {$this->idEstudiante}",
            'docentes' => "id_docente = {$this->idDocente}",
            'solicitudes' => 'id_solicitud IN (' . implode(',', [$this->idSolEst, $this->idSolDoc]) . ')',
        ];

        foreach ($tablas as $table => $where) {
            if ($table === 'estudiantes' && $this->idEstudiante <= 0) {
                continue;
            }
            if ($table === 'docentes' && $this->idDocente <= 0) {
                continue;
            }
            if ($table === 'solicitudes' && ($this->idSolEst <= 0 || $this->idSolDoc <= 0)) {
                continue;
            }
            $cols = $this->pdo->query("SHOW COLUMNS FROM `$table`")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($cols as $c) {
                if (!preg_match('/varchar|text|char/', strtolower((string) $c['Type']))) {
                    continue;
                }
                $col = $c['Field'];
                $n = (int) $this->pdo->query(
                    "SELECT COUNT(*) FROM `$table` WHERE $where AND `$col` LIKE '%??%'"
                )->fetchColumn();
                if ($n > 0) {
                    $this->fail('utf8', "$table.$col tiene ?? ($n)");

                    return;
                }
            }
        }

        $this->ok('utf8', 'Sin caracteres ?? en registros de prueba');
    }

    private function limpiarDatosPrueba(): void
    {
        $solIds = array_filter([$this->idSolEst, $this->idSolDoc]);
        foreach ($solIds as $idSol) {
            $dir = SolicitudesAnexosUpload::directorioSolicitud($idSol);
            if (is_dir($dir)) {
                foreach (glob($dir . DIRECTORY_SEPARATOR . '*') ?: [] as $f) {
                    if (is_file($f)) {
                        @unlink($f);
                    }
                }
                @rmdir($dir);
            }
        }

        if ($solIds !== []) {
            $in = implode(',', array_map('intval', $solIds));
            $this->pdo->exec("DELETE FROM solicitudes WHERE id_solicitud IN ($in)");
        }
        if ($this->idEstudiante > 0) {
            $this->pdo->prepare('DELETE FROM estudiantes WHERE id_estudiante = ?')->execute([$this->idEstudiante]);
        }
        if ($this->idDocente > 0) {
            $this->pdo->prepare('DELETE FROM docentes WHERE id_docente = ?')->execute([$this->idDocente]);
        }

        $this->ok('cleanup', 'Datos y adjuntos de prueba eliminados');
    }

    private function ok(string $name, string $detail): void
    {
        $this->results[] = ['name' => $name, 'ok' => true, 'detail' => $detail];
        $this->passed++;
    }

    private function fail(string $name, string $detail): void
    {
        $this->results[] = ['name' => $name, 'ok' => false, 'detail' => $detail];
        $this->failed++;
    }

    private function report(): void
    {
        echo str_repeat('-', 60) . "\n";
        foreach ($this->results as $r) {
            $icon = $r['ok'] ? '[OK]  ' : '[FAIL]';
            echo sprintf("%s %-28s %s\n", $icon, $r['name'], $r['detail']);
        }
        echo str_repeat('-', 60) . "\n";
        echo "Pasaron: {$this->passed} | Fallaron: {$this->failed}\n\n";
        if ($this->failed === 0) {
            echo "RESULTADO: Todas las operaciones persisten correctamente en MySQL.\n\n";
        } else {
            echo "RESULTADO: Hay errores de persistencia — revise los [FAIL] arriba.\n\n";
        }
    }
}

$keep = in_array('--keep', $argv ?? [], true);
exit((new TestIntegracionBd($keep))->run());
