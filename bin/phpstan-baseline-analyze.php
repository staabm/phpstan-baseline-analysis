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

$app = new \staabm\PHPStanBaselineAnalysis\AnalyzeApplication();

if ($argc <= 1) {
    $app->help();
    exit(1);
}


$format = \staabm\PHPStanBaselineAnalysis\ResultPrinter::FORMAT_TEXT;
if (in_array('--json', $argv)) {
    $format = \staabm\PHPStanBaselineAnalysis\ResultPrinter::FORMAT_JSON;
}

$app->start($argv[1], $format);
exit(0);