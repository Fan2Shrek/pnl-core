<?php

$classLoader = require 'vendor/autoload.php';

if (!isset($_SERVER['SCRIPT_FILENAME'])) {
    print_r('Script filename not found');
    exit(1);
}

$app = require $_SERVER['SCRIPT_FILENAME'];

$runtime = new Pnl\Runtime\PnlRuntime($classLoader);

$resolver = $runtime->getResolver($app);
[$appRunner, $appArgs] = $resolver->resolve();

$app = $appRunner(...$appArgs);

exit($runtime->getRunner($app)->run());
