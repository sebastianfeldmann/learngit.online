<?php

namespace LearnGit\Controller;

class NotFoundController
{
    public function __invoke(): void
    {
        self::render404();
    }

    public static function render404(): void
    {
        http_response_code(404);
        require TPL_DIR . '404.tpl.php';
    }
}
