<?php

namespace staabm\PHPStanBaselineAnalysis\Tests;

use PHPUnit\Framework\TestCase;
use staabm\PHPStanBaselineAnalysis\Application;
use staabm\PHPStanBaselineAnalysis\Baseline;
use staabm\PHPStanBaselineAnalysis\BaselineAnalyzer;
use staabm\PHPStanBaselineAnalysis\ResultPrinter;

class ApplicationTest extends TestCase
{
    function testTextPrinting():void
    {
        $app = new Application();

        ob_start();
        $app->start(__DIR__ . '/fixtures/all-in.neon', ResultPrinter::FORMAT_TEXT);
        $rendered = ob_get_clean();

        $rendered = str_replace(__DIR__, '', $rendered);

        $expected = <<<PHP
Analyzing /fixtures/all-in.neon
  Overall-Class-Cognitive-Complexity: 70

PHP;

        $this->assertSame($expected, $rendered);
    }

    function testJsonPrinting():void
    {
        $app = new Application();

        ob_start();
        $app->start(__DIR__ . '/fixtures/all-in.neon', ResultPrinter::FORMAT_JSON);
        $rendered = ob_get_clean();

        $rendered = str_replace(trim(json_encode(__DIR__), '"'), '', $rendered);

        $expected = <<<PHP
[{"\/fixtures\/all-in.neon":{"Overall-Class-Cognitive-Complexity":70}}]
PHP;

        $this->assertSame($expected, $rendered);
    }

}
