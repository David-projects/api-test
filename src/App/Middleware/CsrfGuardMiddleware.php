<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;

class CsrfGuardMiddleware implements MiddlewareInterface
{
    public function process(callable $next)
    {
        //dd($_REQUEST);
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' ||
            $_SERVER['REQUEST_METHOD'] === 'PATCH'  ||
            $_SERVER['REQUEST_METHOD'] === 'DELETE'
        ) {
            if ($_SESSION['token'] !== $_POST['token']) {
                unset($_SESSION['token']);
                redirectTo('/');
            }
            unset($_SESSION['token']);
        }

        $next();
    }
}
