<?php

namespace staabm\PHPStanBaselineAnalysis\Tests;

use PHPUnit\Framework\TestCase;
use staabm\PHPStanBaselineAnalysis\AnalyzeApplication;
use staabm\PHPStanBaselineAnalysis\Baseline;
use staabm\PHPStanBaselineAnalysis\BaselineAnalyzer;
use staabm\PHPStanBaselineAnalysis\ResultPrinter;
use staabm\PHPStanBaselineAnalysis\TrendApplication;

class TrendApplicationTest extends TestCase
{
    function testSameTrend():void
    {
        $app = new TrendApplication();

        ob_start();
        $app->start(__DIR__.'/fixtures/reference-result.json', __DIR__.'/fixtures/compare-same-result.json');
        $rendered = ob_get_clean();

        $rendered = str_replace(__DIR__, '', $rendered);
        $rendered = $this->normalizeNewlines($rendered);

        $expected = <<<PHP
Analyzing /fixtures/all-in.neon
  Overall-Class-Cognitive-Complexity: 70 -> 70
PHP;
        $expected = $this->normalizeNewlines($expected);

        $this->assertSame($expected, $rendered);
    }

    private function normalizeNewlines(string $string):string {
        return str_replace("\r\n", "\n", $string);
    }
}
