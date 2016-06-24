<?php

namespace Reactor\DbMigration;

use Reactor\CliArguments\ArgumentsParser;

include __DIR__.'/../vendor/autoload.php';

$app = new Application();
$cli = new CliControllers\AppController($app);
$cli->handle(new ArgumentsParser($GLOBALS['argv']));
