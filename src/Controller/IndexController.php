<?php

namespace LearnGit\Controller;

class IndexController
{
    public function __invoke(): void
    {
        // Load taglines config and pick a random one
        $taglines = require CONFIG_DIR . 'taglines.php';
        $tagline = $taglines[array_rand($taglines)];
        
        require TPL_DIR . 'index.tpl.php';
    }
}
