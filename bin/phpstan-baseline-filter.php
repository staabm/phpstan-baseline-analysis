<?php

use staabm\PHPStanBaselineAnalysis\FilterApplication;
use staabm\PHPStanBaselineAnalysis\ResultPrinter;

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

$app = new FilterApplication();

if (in_array('--version', $argv)) {
    echo "PHPStan baseline analysis, version ". \Composer\InstalledVersions::getPrettyVersion('staabm/phpstan-baseline-analysis') ."\n";
    echo "https://github.com/staabm/phpstan-baseline-analysis\n";
    exit(0);
}

if ($argc < 3) {
    $app->help();
    exit(254);
}

try {
    $filterConfig = \staabm\PHPStanBaselineAnalysis\FilterConfig::fromArgs($argv[2]);

    $exitCode = $app->start($argv[1], $filterConfig);
    exit($exitCode);
} catch (\Exception $e) {
    echo 'ERROR: '. $e->getMessage().PHP_EOL.PHP_EOL;
    $app->help();
    exit(254);
}
