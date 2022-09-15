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

        $this->assertSame(35, $result->overallErrors);
        $this->assertSame(70, $result->classesComplexity);
        $this->assertSame(2, $result->deprecations);
        $this->assertSame(5, $result->invalidPhpdocs);
        $this->assertSame(1, $result->unknownTypes);
        $this->assertSame(4, $result->anonymousVariables);
    }

    function testClassComplexity():void
    {
        $analyzer = new BaselineAnalyzer(Baseline::forFile(__DIR__ . '/fixtures/class-complexity.neon'));
        $result = $analyzer->analyze();

        $this->assertSame(6, $result->overallErrors);
        $this->assertSame(50, $result->classesComplexity);
        $this->assertSame(0, $result->deprecations);
        $this->assertSame(0, $result->invalidPhpdocs);
        $this->assertSame(0, $result->unknownTypes);
        $this->assertSame(0, $result->anonymousVariables);
    }

    function testMethodComplexityIgnored():void
    {
        $analyzer = new BaselineAnalyzer(Baseline::forFile(__DIR__ . '/fixtures/method-complexity.neon'));
        $result = $analyzer->analyze();

        $this->assertSame(4, $result->overallErrors);
        $this->assertSame(0, $result->classesComplexity);
        $this->assertSame(0, $result->deprecations);
        $this->assertSame(0, $result->invalidPhpdocs);
        $this->assertSame(0, $result->unknownTypes);
        $this->assertSame(0, $result->anonymousVariables);
    }

    function testDeprecations():void
    {
        $analyzer = new BaselineAnalyzer(Baseline::forFile(__DIR__ . '/fixtures/deprecations.neon'));
        $result = $analyzer->analyze();

        $this->assertSame(12, $result->overallErrors);
        $this->assertSame(0, $result->classesComplexity);
        $this->assertSame(12, $result->deprecations);
        $this->assertSame(0, $result->invalidPhpdocs);
        $this->assertSame(0, $result->unknownTypes);
        $this->assertSame(0, $result->anonymousVariables);
    }

    function testInvalidPhpdocs():void
    {
        $analyzer = new BaselineAnalyzer(Baseline::forFile(__DIR__ . '/fixtures/invalid-phpdocs.neon'));
        $result = $analyzer->analyze();

        $this->assertSame(8, $result->overallErrors);
        $this->assertSame(0, $result->classesComplexity);
        $this->assertSame(0, $result->deprecations);
        $this->assertSame(8, $result->invalidPhpdocs);
        $this->assertSame(0, $result->unknownTypes);
        $this->assertSame(0, $result->anonymousVariables);
    }

    function testUnknownTypes():void
    {
        $analyzer = new BaselineAnalyzer(Baseline::forFile(__DIR__ . '/fixtures/unknown-types.neon'));
        $result = $analyzer->analyze();

        $this->assertSame(7, $result->overallErrors);
        $this->assertSame(0, $result->classesComplexity);
        $this->assertSame(0, $result->deprecations);
        $this->assertSame(0, $result->invalidPhpdocs);
        $this->assertSame(7, $result->unknownTypes);
        $this->assertSame(0, $result->anonymousVariables);
    }

    function testAnonymousVariables():void
    {
        $analyzer = new BaselineAnalyzer(Baseline::forFile(__DIR__ . '/fixtures/anonymous-variables.neon'));
        $result = $analyzer->analyze();

        $this->assertSame(4, $result->overallErrors);
        $this->assertSame(0, $result->classesComplexity);
        $this->assertSame(0, $result->deprecations);
        $this->assertSame(0, $result->invalidPhpdocs);
        $this->assertSame(0, $result->unknownTypes);
        $this->assertSame(4, $result->anonymousVariables);
    }

}
