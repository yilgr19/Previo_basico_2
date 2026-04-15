<?php
declare(strict_types=1);
require_once __DIR__ . '/config/config.php';

use App\Services\SolicitudesAnexosUpload;
use App\Services\SolicitudesService;

require_login();

$idSol = (int) (get('s', '0') ?? '0');
$idx = (int) (get('f', '0') ?? '0');
if ($idSol <= 0 || $idx < 0) {
    http_response_code(400);
    exit('Solicitud no válida.');
}

$rows = load_data('solicitudes');
$sol = null;
foreach ($rows as $r) {
    if ((int) ($r['id_solicitud'] ?? 0) === $idSol) {
        $sol = $r;
        break;
    }
}
if ($sol === null) {
    http_response_code(404);
    exit('No encontrado.');
}

$u = auth_user();
if (!SolicitudesService::usuarioPuedeVerAnexos($u, $sol)) {
    http_response_code(403);
    exit('No autorizado.');
}

$anexos = $sol['anexos_archivos'] ?? [];
if (!is_array($anexos) || !isset($anexos[$idx])) {
    http_response_code(404);
    exit('Adjunto no encontrado.');
}

$meta = $anexos[$idx];
$guardado = (string) ($meta['guardado'] ?? '');
if ($guardado === '' || str_contains($guardado, '..') || str_contains($guardado, '/') || str_contains($guardado, '\\')) {
    http_response_code(400);
    exit('Archivo no válido.');
}

$path = SolicitudesAnexosUpload::directorioSolicitud($idSol) . DIRECTORY_SEPARATOR . $guardado;
if (!is_file($path)) {
    http_response_code(404);
    exit('Archivo no disponible.');
}

$mime = (string) ($meta['mime'] ?? 'application/octet-stream');
$orig = (string) ($meta['original'] ?? 'adjunto');
$disp = 'attachment; filename="' . str_replace('"', '', $orig) . '"';

header('Content-Type: ' . $mime);
header('Content-Disposition: ' . $disp);
header('Content-Length: ' . (string) filesize($path));
readfile($path);
exit;
