<?php
declare(strict_types=1);

namespace App\Controllers;

/** Crea el controlador y ejecuta run(). */
function bootstrap(string $controllerClass, mixed ...$args): void
{
    $controller = new $controllerClass(...$args);
    $controller->run();
}

/**
 * Ejecuta el controlador solo si Apache/PHP abrió este archivo como entrada
 * (evita doble ejecución cuando index.php carga el controlador por autoload).
 */
function dispatch_if_direct(string $entryFile, string $controllerClass, mixed ...$args): void
{
    $script = $_SERVER['SCRIPT_FILENAME'] ?? '';
    if ($script === '') {
        return;
    }
    $normalizedScript = realpath($script);
    $normalizedEntry = realpath($entryFile);
    if ($normalizedScript !== false && $normalizedEntry !== false && $normalizedScript === $normalizedEntry) {
        bootstrap($controllerClass, ...$args);
    }
}
