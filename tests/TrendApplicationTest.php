<?php

namespace staabm\PHPStanBaselineAnalysis\Tests;

use staabm\PHPStanBaselineAnalysis\TrendApplication;

class TrendApplicationTest extends BaseTestCase
{
    function testSameTrend():void
    {
        $app = new TrendApplication();

        ob_start();
        $app->start(__DIR__.'/fixtures/reference-result.json', __DIR__.'/fixtures/compare-same-result.json');
        $rendered = ob_get_clean();

        $rendered = str_replace(__DIR__, '', $rendered);

        $expected = <<<PHP
Analyzing /fixtures/all-in.neon
  Overall-Class-Cognitive-Complexity: 70 -> 70
PHP;

        $this->assertSame($expected, $rendered);
    }
}
