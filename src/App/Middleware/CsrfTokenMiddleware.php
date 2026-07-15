<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\TemplateEngine;

class CsrfTokenMiddleware implements MiddlewareInterface
{
    public function __construct(private TemplateEngine $view)
    {
    }

    public function process(callable $next)
    {
        $_SESSION['token'] = $_SESSION['token'] ?: $this->view->escapeString(bin2hex(random_bytes(44)));
        $this->view->addGlobal('csrfToken', $_SESSION['token']);
        $next();
    }
}
