<?php

namespace staabm\PHPStanBaselineAnalysis\Tests;

use staabm\PHPStanBaselineAnalysis\TrendApplication;

class TrendApplicationTest extends BaseTestCase
{
    function testSameTrend():void
    {
        $app = new TrendApplication();

        ob_start();
        $exitCode = $app->start(__DIR__.'/fixtures/reference-result.json', __DIR__.'/fixtures/compare-same-result.json');
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

PHP;

        $this->assertSame($expected, $rendered);
        $this->assertSame(TrendApplication::EXIT_STEADY, $exitCode);
    }

    function testHigherTrend():void
    {
        $app = new TrendApplication();

        ob_start();
        $exitCode = $app->start(__DIR__.'/fixtures/reference-result.json', __DIR__.'/fixtures/compare-higher-result.json');
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

PHP;

        $this->assertSame($expected, $rendered);
        $this->assertSame(TrendApplication::EXIT_WORSE, $exitCode);
    }

    function testLowerTrend():void
    {
        $app = new TrendApplication();

        ob_start();
        $exitCode = $app->start(__DIR__.'/fixtures/reference-result.json', __DIR__.'/fixtures/compare-lower-result.json');
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

PHP;

        $this->assertSame($expected, $rendered);
        $this->assertSame(TrendApplication::EXIT_IMPROVED, $exitCode);
    }
}
