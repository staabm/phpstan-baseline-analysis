#!/usr/bin/env php
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

$app = new \staabm\PHPStanBaselineAnalysis\Application();
$app->start($argv[1]);