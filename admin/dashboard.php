<?php
declare(strict_types=1);
/**
 * Alias por compatibilidad: el panel de administración pasó a gestion/.
 * Evita bucles si la sesión antigua apuntaba aquí o hay enlaces viejos.
 */
require_once dirname(__DIR__) . '/config/config.php';

redirect('/gestion/dashboard.php');
