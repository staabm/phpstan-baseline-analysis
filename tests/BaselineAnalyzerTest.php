<?php

namespace staabm\PHPStanBaselineAnalysis\Tests;

use PHPUnit\Framework\TestCase;
use staabm\PHPStanBaselineAnalysis\Baseline;
use staabm\PHPStanBaselineAnalysis\BaselineAnalyzer;

class BaselineAnalyzerTest extends TestCase
{
    function testAllInComplexity():void
    {
        $analyzer = new BaselineAnalyzer(Baseline::forFile(__DIR__ . '/fixtures/all-in.neon'));
        $result = $analyzer->analyze();

        $this->assertSame(70, $result->classesComplexity);
    }

    function testClassComplexity():void
    {
        $analyzer = new BaselineAnalyzer(Baseline::forFile(__DIR__ . '/fixtures/class-complexity.neon'));
        $result = $analyzer->analyze();

        $this->assertSame(26, $result->classesComplexity);
    }

    function testMethodComplexityIgnored():void
    {
        $analyzer = new BaselineAnalyzer(Baseline::forFile(__DIR__ . '/fixtures/method-complexity.neon'));
        $result = $analyzer->analyze();

        $this->assertSame(0, $result->classesComplexity);
    }

    function testDeprecations():void
    {
        $analyzer = new BaselineAnalyzer(Baseline::forFile(__DIR__ . '/fixtures/deprecations.neon'));
        $result = $analyzer->analyze();

        $this->assertSame(0, $result->classesComplexity);
        $this->assertSame(3, $result->deprecations);
    }

}
