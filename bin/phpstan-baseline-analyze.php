<?php

use function Safe\ini_set;

error_reporting(E_ALL);
ini_set('display_errors', 'stderr');

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

if (in_array('--version', $argv)) {
    echo "PHPStan baseline analysis, version ". \Composer\InstalledVersions::getPrettyVersion('staabm/phpstan-baseline-analysis') ."\n";
    echo "https://github.com/staabm/phpstan-baseline-analysis\n";
    exit(0);
}

if ($argc <= 1) {
    $app->help();
    exit(254);
}


$format = \staabm\PHPStanBaselineAnalysis\ResultPrinter::FORMAT_TEXT;
if (in_array('--json', $argv)) {
    $format = \staabm\PHPStanBaselineAnalysis\ResultPrinter::FORMAT_JSON;
}

$exitCode = $app->start($argv[1], $format);
exit($exitCode);
