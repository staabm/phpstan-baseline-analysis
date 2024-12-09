<?php

use staabm\PHPStanBaselineAnalysis\ResultPrinter;
use function Safe\ini_set;

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

error_reporting(E_ALL);
ini_set('display_errors', 'stderr');

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


$format = ResultPrinter::FORMAT_TEXT;
$useFileMtime = false;
if (in_array('--json', $argv)) {
    $format = ResultPrinter::FORMAT_JSON;
}

if (in_array('--mtime', $argv)) {
    $useFileMtime = true;
}

$exitCode = $app->start($argv[1], $format, $useFileMtime);
exit($exitCode);
