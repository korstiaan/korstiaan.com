<?php

use Kors\Com\Command\AsseticCommand,
    Kors\Com\Command\SymlinkCommand
    ;

set_time_limit(0);

$app = require __DIR__.'/bootstrap.php';

$application = $app['console'];
$application->add(new SymlinkCommand());
$application->add(new AsseticCommand());

$application->run();
