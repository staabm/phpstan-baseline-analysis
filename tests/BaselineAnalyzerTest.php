<?php

namespace staabm\PHPStanBaselineAnalysis\Tests;

use PHPUnit\Framework\TestCase;
use staabm\PHPStanBaselineAnalysis\Baseline;
use staabm\PHPStanBaselineAnalysis\BaselineAnalyzer;

class BaselineAnalyzerTest extends TestCase
{
    function testAllInComplexity()
    {
        $analyzer = new BaselineAnalyzer(Baseline::forFile(__DIR__ . '/fixtures/all-in.neon'));
        $result = $analyzer->analyze();

        $this->assertSame(70, $result->overallComplexity);
    }

    function testClassComplexity()
    {
        $analyzer = new BaselineAnalyzer(Baseline::forFile(__DIR__ . '/fixtures/class-complexity.neon'));
        $result = $analyzer->analyze();

        $this->assertSame(26, $result->overallComplexity);
    }

    function testMethodComplexityIgnored()
    {
        $analyzer = new BaselineAnalyzer(Baseline::forFile(__DIR__ . '/fixtures/method-complexity.neon'));
        $result = $analyzer->analyze();

        $this->assertSame(0, $result->overallComplexity);
    }

}
