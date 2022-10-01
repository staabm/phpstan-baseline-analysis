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

$app = new \staabm\PHPStanBaselineAnalysis\GraphApplication();

if ($argc <= 1) {
    $app->help();
    exit(254);
}

$exitCode = $app->start($argv[1]);
exit($exitCode);