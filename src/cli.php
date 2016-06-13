<?php

use \Webreactor\CliArguments\ArgumentsParser;

include __DIR__.'/../vendor/autoload.php';

$app = new \Dbml\Application();

$cli = new \Dbml\CliControllers\DbmlController($app);
$cli->handle(new ArgumentsParser($GLOBALS['argv']));
