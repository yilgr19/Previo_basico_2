<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Evidencias adjuntas a solicitudes (capturas, PDF, imágenes).
 */
final class SolicitudesAnexosUpload
{
    private const MAX_BYTES = 5242880;

    private const MAX_FILES = 8;

    /** @var list<string> */
    private const EXT_OK = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'];

    /**
     * @return array{0: list<array{guardado: string, original: string, mime: string, bytes: int}>, 1: ?string}
     */
    public static function guardarParaSolicitud(int $idSolicitud): array
    {
        if ($idSolicitud <= 0) {
            return [[], 'Identificador de solicitud no válido.'];
        }
        if (empty($_FILES['anexos'])) {
            return [[], null];
        }

        $baseDir = self::directorioSolicitud($idSolicitud);
        if (!is_dir($baseDir) && !mkdir($baseDir, 0755, true) && !is_dir($baseDir)) {
            return [[], 'No se pudo crear la carpeta de adjuntos.'];
        }

        $files = self::normalizarFilesArray($_FILES['anexos']);
        if (count($files) > self::MAX_FILES) {
            return [[], 'Máximo ' . self::MAX_FILES . ' archivos por solicitud.'];
        }

        $guardados = [];
        foreach ($files as $f) {
            if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if (($f['error'] ?? 0) !== UPLOAD_ERR_OK) {
                return [[], 'Error al subir uno de los archivos.'];
            }
            $tmp = (string) ($f['tmp_name'] ?? '');
            if ($tmp === '' || !is_uploaded_file($tmp)) {
                return [[], 'Archivo inválido.'];
            }
            $orig = (string) ($f['name'] ?? 'archivo');
            $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
            if (!in_array($ext, self::EXT_OK, true)) {
                return [[], 'Tipo no permitido. Use PDF o imagen (JPG, PNG, GIF, WEBP).'];
            }
            $size = (int) ($f['size'] ?? 0);
            if ($size <= 0 || $size > self::MAX_BYTES) {
                return [[], 'Cada archivo debe ser menor a 5 MB.'];
            }
            $mime = self::mimeSeguro($tmp, $ext);
            $nombreFs = uniqid('ev_', true) . '.' . $ext;
            $dest = $baseDir . DIRECTORY_SEPARATOR . $nombreFs;
            if (!move_uploaded_file($tmp, $dest)) {
                return [[], 'No se pudo guardar el archivo.'];
            }
            $guardados[] = [
                'guardado' => $nombreFs,
                'original' => $orig,
                'mime' => $mime,
                'bytes' => $size,
            ];
        }

        return [$guardados, null];
    }

    public static function directorioSolicitud(int $idSolicitud): string
    {
        return ROOT_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'solicitudes' . DIRECTORY_SEPARATOR . $idSolicitud;
    }

    /**
     * @return list<array{name: string, type: int, tmp_name: string, error: int, size: int}>
     */
    private static function normalizarFilesArray(array $filesField): array
    {
        if (!isset($filesField['name'])) {
            return [];
        }
        if (is_array($filesField['name'])) {
            $out = [];
            $n = count($filesField['name']);
            for ($i = 0; $i < $n; $i++) {
                $out[] = [
                    'name' => (string) ($filesField['name'][$i] ?? ''),
                    'type' => (int) ($filesField['type'][$i] ?? 0),
                    'tmp_name' => (string) ($filesField['tmp_name'][$i] ?? ''),
                    'error' => (int) ($filesField['error'][$i] ?? 0),
                    'size' => (int) ($filesField['size'][$i] ?? 0),
                ];
            }

            return $out;
        }

        return [[
            'name' => (string) ($filesField['name'] ?? ''),
            'type' => (int) ($filesField['type'] ?? 0),
            'tmp_name' => (string) ($filesField['tmp_name'] ?? ''),
            'error' => (int) ($filesField['error'] ?? 0),
            'size' => (int) ($filesField['size'] ?? 0),
        ]];
    }

    private static function mimeSeguro(string $tmp, string $ext): string
    {
        if (function_exists('mime_content_type')) {
            $m = @mime_content_type($tmp);
            if (is_string($m) && $m !== '') {
                return $m;
            }
        }

        return match ($ext) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };
    }
}
