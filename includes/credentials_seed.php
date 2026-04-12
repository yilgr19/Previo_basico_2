<?php
declare(strict_types=1);

/**
 * @deprecated El login usa data/*.json (ver attempt_login en auth.php). Archivo solo de referencia.
 */
function auth_credentials_data(): array
{
    return [
        'administradores' => [
            [
                'id_admin' => 1,
                'nombre' => 'Administrador Principal',
                'correo' => 'admin@academico.edu',
                'clave' => 'admin123',
            ],
        ],
        'docentes' => [
            [
                'id_docente' => 1,
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'documento' => '12345678',
                'clave' => 'docente123',
            ],
        ],
        'estudiantes' => [
            [
                'id_estudiante' => 1,
                'nombre' => 'María',
                'apellido' => 'García',
                'documento' => '87654321',
                'clave' => 'estudiante123',
            ],
        ],
    ];
}
