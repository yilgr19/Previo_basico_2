<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Evidencias adjuntas a solicitudes (capturas, PDF, imágenes).
 */
final class SolicitudesAnexosUpload
{
    private const MAX_BYTES = 5242880;

    private const MAX_TOTAL_FILES = 15;

    /** @var list<string> */
    private const EXT_OK = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'];

    /**
     * Compatibilidad: solo el campo `anexos` (múltiple).
     *
     * @return array{0: list<array<string, mixed>>, 1: ?string}
     */
    public static function guardarParaSolicitud(int $idSolicitud): array
    {
        return self::guardarMultiplesCampos($idSolicitud, [
            ['input' => 'anexos', 'categoria' => 'general', 'multiple' => true],
        ]);
    }

    /**
     * Varios inputs de archivo con etiqueta (categoría) para reglas de evidencia.
     *
     * @param list<array{input: string, categoria: string, multiple?: bool}> $grupos
     * @return array{0: list<array<string, mixed>>, 1: ?string}
     */
    public static function guardarMultiplesCampos(int $idSolicitud, array $grupos): array
    {
        if ($idSolicitud <= 0) {
            return [[], 'Identificador de solicitud no válido.'];
        }

        $baseDir = self::directorioSolicitud($idSolicitud);
        if (!is_dir($baseDir) && !mkdir($baseDir, 0755, true) && !is_dir($baseDir)) {
            return [[], 'No se pudo crear la carpeta de adjuntos.'];
        }

        $guardados = [];
        foreach ($grupos as $g) {
            $input = (string) ($g['input'] ?? '');
            $categoria = (string) ($g['categoria'] ?? 'general');
            $multiple = (bool) ($g['multiple'] ?? true);
            if ($input === '' || empty($_FILES[$input])) {
                continue;
            }
            $files = self::normalizarFilesArray($_FILES[$input]);
            if (!$multiple && count($files) > 1) {
                $files = [$files[0]];
            }
            foreach ($files as $f) {
                if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                [$meta, $err] = self::moverUnArchivo($baseDir, $f, $categoria);
                if ($err !== null) {
                    return [[], $err];
                }
                if ($meta !== null) {
                    $guardados[] = $meta;
                    if (count($guardados) > self::MAX_TOTAL_FILES) {
                        return [[], 'Máximo ' . self::MAX_TOTAL_FILES . ' archivos por solicitud.'];
                    }
                }
            }
        }

        return [$guardados, null];
    }

    /** Indica si hay al menos un archivo válido en el input (para validaciones condicionales). */
    public static function hayArchivoSubidoOk(string $inputName): bool
    {
        if ($inputName === '' || empty($_FILES[$inputName])) {
            return false;
        }
        foreach (self::normalizarFilesArray($_FILES[$inputName]) as $f) {
            if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if (($f['error'] ?? 0) === UPLOAD_ERR_OK) {
                $tmp = (string) ($f['tmp_name'] ?? '');
                if ($tmp !== '' && is_uploaded_file($tmp)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array{name: string, type: int, tmp_name: string, error: int, size: int} $f
     * @return array{0: ?array<string, mixed>, 1: ?string}
     */
    private static function moverUnArchivo(string $baseDir, array $f, string $categoria): array
    {
        if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return [null, null];
        }
        if (($f['error'] ?? 0) !== UPLOAD_ERR_OK) {
            return [null, 'Error al subir uno de los archivos.'];
        }
        $tmp = (string) ($f['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return [null, 'Archivo inválido.'];
        }
        $orig = (string) ($f['name'] ?? 'archivo');
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        if (!in_array($ext, self::EXT_OK, true)) {
            return [null, 'Tipo no permitido. Use PDF o imagen (JPG, PNG, GIF, WEBP).'];
        }
        $size = (int) ($f['size'] ?? 0);
        if ($size <= 0 || $size > self::MAX_BYTES) {
            return [null, 'Cada archivo debe ser menor a 5 MB.'];
        }
        $mime = self::mimeSeguro($tmp, $ext);
        $nombreFs = uniqid('ev_', true) . '.' . $ext;
        $dest = $baseDir . DIRECTORY_SEPARATOR . $nombreFs;
        if (!move_uploaded_file($tmp, $dest)) {
            return [null, 'No se pudo guardar el archivo.'];
        }

        return [[
            'guardado' => $nombreFs,
            'original' => $orig,
            'mime' => $mime,
            'bytes' => $size,
            'categoria' => $categoria,
        ], null];
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
