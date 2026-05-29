<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Solicitud de soportes por parte de gestión y carga por el estudiante.
 */
final class SolicitudDocumentosService
{
    private const CAMPO_SOLICITUD = 'docs_pendientes_estudiante';

    /** @return list<array{codigo: string, nombre: string}> */
    public static function categoriasSolicitables(): array
    {
        if (function_exists('diccionario_categorias_documento_admin')) {
            return diccionario_categorias_documento_admin();
        }

        return [
            ['codigo' => 'soporte_medico', 'nombre' => 'Soporte médico'],
            ['codigo' => 'carta_aceptacion', 'nombre' => 'Carta de aceptación / orden'],
            ['codigo' => 'recibo_pago', 'nombre' => 'Recibo de pago'],
            ['codigo' => 'general', 'nombre' => 'Evidencias generales'],
            ['codigo' => 'doc_terceros', 'nombre' => 'Documentación de terceros'],
            ['codigo' => 'formato_institucional', 'nombre' => 'Formato institucional'],
        ];
    }

    public static function categoriaValida(string $categoria): bool
    {
        $categoria = strtolower(trim($categoria));
        foreach (self::categoriasSolicitables() as $c) {
            if (($c['codigo'] ?? '') === $categoria) {
                return true;
            }
        }

        return false;
    }

    /** @return array{input: string, categoria: string, multiple: bool} */
    public static function grupoUploadParaCategoria(string $categoria): array
    {
        $categoria = strtolower(trim($categoria));

        return [
            'input' => $categoria === 'general' ? 'anexos' : $categoria,
            'categoria' => $categoria,
            'multiple' => $categoria === 'general',
        ];
    }

    /** @return list<array<string, mixed>> */
    public static function listarPendientes(array $solicitud, bool $soloAbiertos = true): array
    {
        $docs = $solicitud[self::CAMPO_SOLICITUD] ?? [];
        if (!is_array($docs)) {
            return [];
        }
        $out = [];
        foreach ($docs as $d) {
            if (!is_array($d)) {
                continue;
            }
            if ($soloAbiertos && trim((string) ($d['cumplido_en'] ?? '')) !== '') {
                continue;
            }
            $out[] = $d;
        }

        return $out;
    }

    public static function tienePendientes(array $solicitud): bool
    {
        return self::listarPendientes($solicitud, true) !== [];
    }

    /** @return array{0: string, 1: string} */
    public static function solicitarDesdeAdmin(int $idSolicitud): array
    {
        $categoria = strtolower(trim((string) post('doc_categoria', '')));
        $mensaje = trim((string) post('doc_mensaje', ''));

        if ($idSolicitud <= 0) {
            return ['Solicitud no válida.', 'warning'];
        }
        if (!self::categoriaValida($categoria)) {
            return ['Seleccione el tipo de documento a solicitar.', 'warning'];
        }
        if ($mensaje === '' || (function_exists('mb_strlen') ? mb_strlen($mensaje, 'UTF-8') : strlen($mensaje)) < 5) {
            return ['Indique un mensaje para el estudiante (mínimo 5 caracteres).', 'warning'];
        }

        $rows = load_data('solicitudes');
        $found = false;
        foreach ($rows as &$s) {
            if ((int) ($s['id_solicitud'] ?? 0) !== $idSolicitud) {
                continue;
            }
            $s = SolicitudesService::normalizarParaVista($s);
            if ((int) ($s['id_estudiante'] ?? 0) <= 0) {
                return ['Solo aplica a solicitudes de estudiantes.', 'warning'];
            }

            $pendientes = self::listarPendientes($s, true);
            foreach ($pendientes as $p) {
                if (($p['categoria'] ?? '') === $categoria) {
                    return ['Ya hay una solicitud abierta de ese documento. Espere a que el estudiante lo cargue o cancélela.', 'warning'];
                }
            }

            if (!isset($s[self::CAMPO_SOLICITUD]) || !is_array($s[self::CAMPO_SOLICITUD])) {
                $s[self::CAMPO_SOLICITUD] = [];
            }

            $s[self::CAMPO_SOLICITUD][] = [
                'id' => 0,
                'categoria' => $categoria,
                'mensaje' => $mensaje,
                'solicitado_en' => fecha_hora_colombia(),
                'cumplido_en' => '',
                'notif_pendiente' => true,
            ];
            $s['notif_pendiente_est'] = true;
            $s['notif_nueva_gestion'] = false;
            $found = true;
            break;
        }
        unset($s);

        if (!$found) {
            return ['Solicitud no encontrada.', 'warning'];
        }

        save_data('solicitudes', $rows);

        $nombre = function_exists('solicitud_etiqueta_categoria_anexo')
            ? solicitud_etiqueta_categoria_anexo($categoria)
            : $categoria;

        return ['Documento solicitado al estudiante: ' . $nombre . '.', 'success'];
    }

    /** @return array{0: string, 1: string} */
    public static function subirDesdeEstudiante(int $idEstudiante, int $idSolicitud, string $categoria): array
    {
        if ($idEstudiante <= 0 || $idSolicitud <= 0) {
            return ['Datos no válidos.', 'warning'];
        }
        $categoria = strtolower(trim($categoria));
        if (!self::categoriaValida($categoria)) {
            return ['Tipo de documento no válido.', 'warning'];
        }

        $rows = load_data('solicitudes');
        $found = false;
        foreach ($rows as &$s) {
            if ((int) ($s['id_solicitud'] ?? 0) !== $idSolicitud) {
                continue;
            }
            $s = SolicitudesService::normalizarParaVista($s);
            if ((int) ($s['id_estudiante'] ?? 0) !== $idEstudiante) {
                return ['No puede modificar esta solicitud.', 'warning'];
            }

            $idxPendiente = null;
            $docs = $s[self::CAMPO_SOLICITUD] ?? [];
            if (!is_array($docs)) {
                $docs = [];
            }
            foreach ($docs as $i => $d) {
                if (!is_array($d)) {
                    continue;
                }
                if (($d['categoria'] ?? '') === $categoria && trim((string) ($d['cumplido_en'] ?? '')) === '') {
                    $idxPendiente = $i;
                    break;
                }
            }
            if ($idxPendiente === null) {
                return ['No hay una solicitud abierta de ese documento.', 'warning'];
            }

            $grupo = self::grupoUploadParaCategoria($categoria);
            [$nuevos, $err] = SolicitudesAnexosUpload::guardarMultiplesCampos($idSolicitud, [$grupo]);
            if ($err !== null) {
                return [$err, 'warning'];
            }
            if ($nuevos === []) {
                return ['Seleccione un archivo para adjuntar.', 'warning'];
            }

            $existentes = is_array($s['anexos_archivos'] ?? null) ? $s['anexos_archivos'] : [];
            $total = count($existentes) + count($nuevos);
            if ($total > 15) {
                return ['Máximo 15 archivos por solicitud.', 'warning'];
            }

            $s['anexos_archivos'] = array_merge($existentes, $nuevos);
            $ahora = fecha_hora_colombia();
            $docs[$idxPendiente]['cumplido_en'] = $ahora;
            $docs[$idxPendiente]['notif_pendiente'] = false;
            $s[self::CAMPO_SOLICITUD] = $docs;
            $s['notif_nueva_gestion'] = true;

            if (!in_array((string) ($s['estado'] ?? ''), ['aprobada', 'rechazada'], true)) {
                $cod = function_exists('solicitud_estado_a_codigo')
                    ? solicitud_estado_a_codigo((string) ($s['estado'] ?? ''))
                    : (string) ($s['estado'] ?? '');
                if ($cod === 'pendiente') {
                    $s['estado'] = 'en_revision';
                }
            }

            $found = true;
            break;
        }
        unset($s);

        if (!$found) {
            return ['Solicitud no encontrada.', 'warning'];
        }

        save_data('solicitudes', $rows);

        $nombre = function_exists('solicitud_etiqueta_categoria_anexo')
            ? solicitud_etiqueta_categoria_anexo($categoria)
            : $categoria;

        return [$nombre . ' cargado correctamente. Gestión académica fue notificada.', 'success'];
    }

    public static function conteoNotificacionesDoc(?array $user): int
    {
        if ($user === null || (string) ($user['rol'] ?? '') !== \ROLE_ESTUDIANTE) {
            return 0;
        }
        $id = (int) ($user['id'] ?? 0);
        if ($id <= 0) {
            return 0;
        }
        $n = 0;
        foreach (load_data('solicitudes') as $s) {
            $s = SolicitudesService::normalizarParaVista($s);
            if ((int) ($s['id_estudiante'] ?? 0) !== $id) {
                continue;
            }
            foreach (self::listarPendientes($s, true) as $d) {
                if (!empty($d['notif_pendiente'])) {
                    $n++;
                }
            }
        }

        return $n;
    }

    /**
     * @return list<array{id_solicitud: int, tipo: string, categoria: string, categoria_nombre: string, mensaje: string, fecha: string}>
     */
    public static function resumenNotificacionesDoc(?array $user, int $limit = 6): array
    {
        if ($user === null || $limit <= 0 || (string) ($user['rol'] ?? '') !== \ROLE_ESTUDIANTE) {
            return [];
        }
        $id = (int) ($user['id'] ?? 0);
        if ($id <= 0) {
            return [];
        }

        $cand = [];
        foreach (load_data('solicitudes') as $s) {
            $s = SolicitudesService::normalizarParaVista($s);
            if ((int) ($s['id_estudiante'] ?? 0) !== $id) {
                continue;
            }
            foreach (self::listarPendientes($s, true) as $d) {
                if (empty($d['notif_pendiente'])) {
                    continue;
                }
                $cand[] = [$s, $d];
            }
        }

        usort($cand, static fn ($a, $b) => strcmp((string) ($b[1]['solicitado_en'] ?? ''), (string) ($a[1]['solicitado_en'] ?? '')));
        $cand = array_slice($cand, 0, $limit);
        $out = [];
        foreach ($cand as [$s, $d]) {
            $cat = (string) ($d['categoria'] ?? '');
            $out[] = [
                'id_solicitud' => (int) ($s['id_solicitud'] ?? 0),
                'tipo' => function_exists('solicitud_tipo_etiqueta') ? solicitud_tipo_etiqueta($s) : '',
                'categoria' => $cat,
                'categoria_nombre' => function_exists('solicitud_etiqueta_categoria_anexo')
                    ? solicitud_etiqueta_categoria_anexo($cat)
                    : $cat,
                'mensaje' => (string) ($d['mensaje'] ?? ''),
                'fecha' => (string) ($d['solicitado_en'] ?? ''),
            ];
        }

        return $out;
    }

    public static function marcarNotifDocVista(int $idEstudiante, int $idSolicitud, string $categoria): void
    {
        $categoria = strtolower(trim($categoria));
        if ($idEstudiante <= 0 || $idSolicitud <= 0 || $categoria === '') {
            return;
        }

        $rows = load_data('solicitudes');
        $changed = false;
        foreach ($rows as &$s) {
            if ((int) ($s['id_solicitud'] ?? 0) !== $idSolicitud || (int) ($s['id_estudiante'] ?? 0) !== $idEstudiante) {
                continue;
            }
            $docs = $s[self::CAMPO_SOLICITUD] ?? [];
            if (!is_array($docs)) {
                break;
            }
            foreach ($docs as &$d) {
                if (!is_array($d)) {
                    continue;
                }
                if (($d['categoria'] ?? '') === $categoria && trim((string) ($d['cumplido_en'] ?? '')) === '') {
                    $d['notif_pendiente'] = false;
                    $changed = true;
                }
            }
            unset($d);
            $s[self::CAMPO_SOLICITUD] = $docs;
            break;
        }
        unset($s);

        if ($changed) {
            save_data('solicitudes', $rows);
        }
    }

    /** @return ?array<string, mixed> */
    public static function pendienteParaEstudiante(array $solicitud, string $categoria): ?array
    {
        $categoria = strtolower(trim($categoria));
        foreach (self::listarPendientes($solicitud, true) as $d) {
            if (($d['categoria'] ?? '') === $categoria) {
                return $d;
            }
        }

        return null;
    }
}
