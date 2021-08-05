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
  Overall-Class-Cognitive-Complexity: 70 -> 70 => good

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
  Overall-Class-Cognitive-Complexity: 70 -> 90 => worse

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
  Overall-Class-Cognitive-Complexity: 70 -> 50 => improved

PHP;

        $this->assertSame($expected, $rendered);
        $this->assertSame(TrendApplication::EXIT_IMPROVED, $exitCode);
    }
}
