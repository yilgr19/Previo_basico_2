<?php
declare(strict_types=1);

namespace App\Controllers\Gestion;

require_once dirname(__DIR__) . '/init.php';
require_once dirname(__DIR__) . '/bootstrap.php';

final class SolicitudesSedeOcanaController extends SolicitudesController
{
    public function __construct()
    {
        parent::__construct(2);
    }
}

\App\Controllers\dispatch_if_direct(__FILE__, SolicitudesSedeOcanaController::class);
