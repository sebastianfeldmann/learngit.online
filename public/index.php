<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/config.php';

use LearnGit\App;
use LearnGit\Request;

$app = new App(new Request($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']));
$app->run();

exit;
