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

error_reporting(E_ALL);
ini_set('display_errors', 'stderr');

$app = new \staabm\PHPStanBaselineAnalysis\TrendApplication();

if (in_array('--version', $argv)) {
    echo "PHPStan baseline analysis, version ". \Composer\InstalledVersions::getPrettyVersion('staabm/phpstan-baseline-analysis') ."\n";
    echo "https://github.com/staabm/phpstan-baseline-analysis\n";
    exit(0);
}

if ($argc <= 2) {
    $app->help();
    exit(254);
}

$exitCode = $app->start($argv[1], $argv[2], extractOutputFormat($argv));
exit($exitCode);

/**
 * @param list<string> $args
 * @return \staabm\PHPStanBaselineAnalysis\TrendApplication::OUTPUT_FORMAT_*
 */
function extractOutputFormat(array $args): string
{
    foreach($args as $arg) {
        if (false === strpos($arg, '--format=')) {
            continue;
        }

        $format = substr($arg, strlen('--format='));
        if (in_array($format, \staabm\PHPStanBaselineAnalysis\TrendApplication::getAllowedOutputFormats(), true)) {
            return $format;
        }

        throw new \InvalidArgumentException(sprintf('Invalid output format "%s".', $format));
    }

    return \staabm\PHPStanBaselineAnalysis\TrendApplication::OUTPUT_FORMAT_DEFAULT;
}
