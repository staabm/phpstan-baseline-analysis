<?php

namespace staabm\PHPStanBaselineAnalysis\Tests;

use Safe\DateTimeImmutable;
use staabm\PHPStanBaselineAnalysis\AnalyzeApplication;
use staabm\PHPStanBaselineAnalysis\ResultPrinter;

use function Safe\filemtime;
use function Safe\json_encode;
use function Safe\ob_start;

class AnalyzeApplicationTest extends BaseTestCase
{
    function testTextPrinting():void
    {
        $app = new AnalyzeApplication();

        ob_start();
        $exitCode = $app->start(__DIR__ . '/fixtures/all-in.neon', ResultPrinter::FORMAT_TEXT);
        $rendered = ob_get_clean();

        $rendered = str_replace(__DIR__, '', $rendered);

        $expectedDate = DateTimeImmutable::createFromFormat("U", (string) filemtime(__DIR__ . '/fixtures/all-in.neon'))->format(ResultPrinter::DATE_FORMAT);
        $expected = <<<PHP
Analyzing /fixtures/all-in.neon
  Date: {$expectedDate}
  Overall-Errors: 41
  Classes-Cognitive-Complexity: 70
  Deprecations: 2
  Invalid-Phpdocs: 5
  Unknown-Types: 1
  Anonymous-Variables: 4
  Native-Property-Type-Coverage: 1
  Native-Param-Type-Coverage: 27
  Native-Return-Type-Coverage: 4
  Unused-Symbols: 3

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

        $expectedDate = DateTimeImmutable::createFromFormat("U", (string) filemtime(__DIR__ . '/fixtures/all-in.neon'))->format(ResultPrinter::DATE_FORMAT);
        $expected = <<<PHP
[{"\/fixtures\/all-in.neon":{"Date":"{$expectedDate}","Overall-Errors":41,"Classes-Cognitive-Complexity":70,"Deprecations":2,"Invalid-Phpdocs":5,"Unknown-Types":1,"Anonymous-Variables":4,"Native-Property-Type-Coverage":1,"Native-Param-Type-Coverage":27,"Native-Return-Type-Coverage":4,"Unused-Symbols":3}}]
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
