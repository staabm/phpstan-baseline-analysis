<?php

// Finding composer

$paths = [
    __DIR__.'/../vendor/autoload.php',
    __DIR__.'/../../../autoload.php',
];

foreach ($paths as $path) {
    if (file_exists($path)) {
        include $path;
        break;
    }
}

$app = new \staabm\PHPStanBaselineAnalysis\TrendApplication();

if ($argc <= 2) {
    $app->help();
    exit(254);
}

$exitCode = $app->start($argv[1], $argv[2]);
exit($exitCode);