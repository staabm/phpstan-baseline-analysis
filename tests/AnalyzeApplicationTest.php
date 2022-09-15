<?php

namespace staabm\PHPStanBaselineAnalysis\Tests;

use staabm\PHPStanBaselineAnalysis\AnalyzeApplication;
use staabm\PHPStanBaselineAnalysis\ResultPrinter;

class AnalyzeApplicationTest extends BaseTestCase
{
    function testTextPrinting():void
    {
        $app = new AnalyzeApplication();

        ob_start();
        $exitCode = $app->start(__DIR__ . '/fixtures/all-in.neon', ResultPrinter::FORMAT_TEXT);
        $rendered = ob_get_clean();

        $rendered = str_replace(__DIR__, '', $rendered);

        $expected = <<<PHP
Analyzing /fixtures/all-in.neon
  Overall-Errors: 35
  Classes-Cognitive-Complexity: 70
  Deprecations: 2
  Invalid-Phpdocs: 5
  Unknown-Types: 1
  Anonymous-Variables: 4

PHP;

        $this->assertSame($expected, $rendered);
        $this->assertSame(0, $exitCode);
    }

    function testJsonPrinting():void
    {
        $app = new AnalyzeApplication();

        ob_start();
        $exitCode = $app->start(__DIR__ . '/fixtures/all-in.neon', ResultPrinter::FORMAT_JSON);
        $rendered = ob_get_clean();

        $rendered = str_replace(trim(json_encode(__DIR__), '"'), '', $rendered);

        $expected = <<<PHP
[{"\/fixtures\/all-in.neon":{"Overall-Errors":35,"Classes-Cognitive-Complexity":70,"Deprecations":2,"Invalid-Phpdocs":5,"Unknown-Types":1,"Anonymous-Variables":4}}]
PHP;

        $this->assertSame($expected, $rendered);
        $this->assertSame(0, $exitCode);
    }

    function testNoMatchingGlob():void
    {
        $app = new AnalyzeApplication();

        ob_start();
        $exitCode = $app->start('this/file/does/not/exist*baseline.neon', ResultPrinter::FORMAT_TEXT);
        $rendered = ob_get_clean();

        $this->assertSame('', $rendered);
        $this->assertSame(1, $exitCode);
    }
}
