<?php

include __dir__.'/../vendor/autoload.php';

$app = new \Dbml\Application();

$cli = new \Dbml\CliControllers\DbmlController($app);
$cli->handle();
