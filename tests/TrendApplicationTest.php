<?php

namespace staabm\PHPStanBaselineAnalysis\Tests;

use staabm\PHPStanBaselineAnalysis\TrendApplication;

class TrendApplicationTest extends BaseTestCase
{
    function testSameTrend():void
    {
        $app = new TrendApplication();

        ob_start();
        $exitCode = $app->start(__DIR__.'/fixtures/reference-result.json', __DIR__.'/fixtures/compare-same-result.json', TrendApplication::OUTPUT_FORMAT_DEFAULT);
        $rendered = ob_get_clean();

        $rendered = str_replace(__DIR__, '', $rendered);

        $expected = <<<PHP
Analyzing Trend for /fixtures/all-in.neon
  Overall-Errors: 18 -> 18 => good
  Classes-Cognitive-Complexity: 70 -> 70 => good
  Deprecations: 1 -> 1 => good
  Invalid-Phpdocs: 3 -> 3 => good
  Unknown-Types: 5 -> 5 => good
  Anonymous-Variables: 2 -> 2 => good
  Unused-Symbols: 1 -> 1 => good
  Native-Return-Type-Coverage: 2 -> 2 => good
  Native-Property-Type-Coverage: 3 -> 3 => good
  Native-Param-Type-Coverage: 4 -> 4 => good

PHP;

        $this->assertSame($expected, $rendered);
        $this->assertSame(TrendApplication::EXIT_STEADY, $exitCode);
    }

    function testHigherTrend():void
    {
        $app = new TrendApplication();

        ob_start();
        $exitCode = $app->start(__DIR__.'/fixtures/reference-result.json', __DIR__.'/fixtures/compare-higher-result.json', TrendApplication::OUTPUT_FORMAT_DEFAULT);
        $rendered = ob_get_clean();

        $rendered = str_replace(__DIR__, '', $rendered);

        $expected = <<<PHP
Analyzing Trend for /fixtures/all-in.neon
  Overall-Errors: 18 -> 24 => worse
  Classes-Cognitive-Complexity: 70 -> 90 => worse
  Deprecations: 1 -> 10 => worse
  Invalid-Phpdocs: 3 -> 30 => worse
  Unknown-Types: 5 -> 15 => worse
  Anonymous-Variables: 2 -> 5 => worse
  Unused-Symbols: 1 -> 10 => worse
  Native-Return-Type-Coverage: 2 -> 20 => improved
  Native-Property-Type-Coverage: 3 -> 30 => improved
  Native-Param-Type-Coverage: 4 -> 40 => improved

PHP;

        $this->assertSame($expected, $rendered);
        $this->assertSame(TrendApplication::EXIT_WORSE, $exitCode);
    }

    function testLowerTrend():void
    {
        $app = new TrendApplication();

        ob_start();
        $exitCode = $app->start(__DIR__.'/fixtures/reference-result.json', __DIR__.'/fixtures/compare-lower-result.json', TrendApplication::OUTPUT_FORMAT_DEFAULT);
        $rendered = ob_get_clean();

        $rendered = str_replace(__DIR__, '', $rendered);

        $expected = <<<PHP
Analyzing Trend for /fixtures/all-in.neon
  Overall-Errors: 18 -> 10 => improved
  Classes-Cognitive-Complexity: 70 -> 50 => improved
  Deprecations: 1 -> 0 => improved
  Invalid-Phpdocs: 3 -> 1 => improved
  Unknown-Types: 5 -> 3 => improved
  Anonymous-Variables: 2 -> 1 => improved
  Unused-Symbols: 1 -> 0 => improved
  Native-Return-Type-Coverage: 2 -> 1 => worse
  Native-Property-Type-Coverage: 3 -> 2 => worse
  Native-Param-Type-Coverage: 4 -> 3 => worse

PHP;

        $this->assertSame($expected, $rendered);
        $this->assertSame(TrendApplication::EXIT_WORSE, $exitCode);
    }

    function testSameTrendFormatJson():void
    {
        $app = new TrendApplication();

        ob_start();
        $exitCode = $app->start(__DIR__.'/fixtures/reference-result.json', __DIR__.'/fixtures/compare-same-result.json', TrendApplication::OUTPUT_FORMAT_JSON);
        $rendered = ob_get_clean();

        $rendered = str_replace(__DIR__, '', $rendered);

        $expected = <<<PHP
[{"headline":"Analyzing Trend for \/fixtures\/all-in.neon","results":{"Overall-Errors":{"reference":18,"comparing":18,"trend":"good"},"Classes-Cognitive-Complexity":{"reference":70,"comparing":70,"trend":"good"},"Deprecations":{"reference":1,"comparing":1,"trend":"good"},"Invalid-Phpdocs":{"reference":3,"comparing":3,"trend":"good"},"Unknown-Types":{"reference":5,"comparing":5,"trend":"good"},"Anonymous-Variables":{"reference":2,"comparing":2,"trend":"good"},"Unused-Symbols":{"reference":1,"comparing":1,"trend":"good"},"Native-Return-Type-Coverage":{"reference":2,"comparing":2,"trend":"good"},"Native-Property-Type-Coverage":{"reference":3,"comparing":3,"trend":"good"},"Native-Param-Type-Coverage":{"reference":4,"comparing":4,"trend":"good"}}}]
PHP;

        $this->assertSame($expected, $rendered);
        $this->assertSame(TrendApplication::EXIT_STEADY, $exitCode);
    }

    function testHigherTrendFormatJson():void
    {
        $app = new TrendApplication();

        ob_start();
        $exitCode = $app->start(__DIR__.'/fixtures/reference-result.json', __DIR__.'/fixtures/compare-higher-result.json', TrendApplication::OUTPUT_FORMAT_JSON);
        $rendered = ob_get_clean();

        $rendered = str_replace(__DIR__, '', $rendered);

        $expected = <<<PHP
[{"headline":"Analyzing Trend for \/fixtures\/all-in.neon","results":{"Overall-Errors":{"reference":18,"comparing":24,"trend":"worse"},"Classes-Cognitive-Complexity":{"reference":70,"comparing":90,"trend":"worse"},"Deprecations":{"reference":1,"comparing":10,"trend":"worse"},"Invalid-Phpdocs":{"reference":3,"comparing":30,"trend":"worse"},"Unknown-Types":{"reference":5,"comparing":15,"trend":"worse"},"Anonymous-Variables":{"reference":2,"comparing":5,"trend":"worse"},"Unused-Symbols":{"reference":1,"comparing":10,"trend":"worse"}}}]
PHP;

        $this->assertSame($expected, $rendered);
        $this->assertSame(TrendApplication::EXIT_WORSE, $exitCode);
    }

    function testLowerTrendFormatJson():void
    {
        $app = new TrendApplication();

        ob_start();
        $exitCode = $app->start(__DIR__.'/fixtures/reference-result.json', __DIR__.'/fixtures/compare-lower-result.json', TrendApplication::OUTPUT_FORMAT_JSON);
        $rendered = ob_get_clean();

        $rendered = str_replace(__DIR__, '', $rendered);

        $expected = <<<PHP
[{"headline":"Analyzing Trend for \/fixtures\/all-in.neon","results":{"Overall-Errors":{"reference":18,"comparing":10,"trend":"improved"},"Classes-Cognitive-Complexity":{"reference":70,"comparing":50,"trend":"improved"},"Deprecations":{"reference":1,"comparing":0,"trend":"improved"},"Invalid-Phpdocs":{"reference":3,"comparing":1,"trend":"improved"},"Unknown-Types":{"reference":5,"comparing":3,"trend":"improved"},"Anonymous-Variables":{"reference":2,"comparing":1,"trend":"improved"},"Unused-Symbols":{"reference":1,"comparing":0,"trend":"improved"}}}]
PHP;

        $this->assertSame($expected, $rendered);
        $this->assertSame(TrendApplication::EXIT_IMPROVED, $exitCode);
    }
}
