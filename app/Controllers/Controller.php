<?php
declare(strict_types=1);

namespace App\Controllers;

abstract class Controller
{
    protected function render(string $viewRelativeToViews, array $data = []): void
    {
        extract($data, EXTR_OVERWRITE);
        require PARTIALS_PATH . '/header.php';
        require VIEWS_PATH . '/' . $viewRelativeToViews;
        require PARTIALS_PATH . '/footer.php';
    }
}
