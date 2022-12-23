<?php

namespace staabm\PHPStanBaselineAnalysis\Tests;

use PHPUnit\Framework\TestCase;
use staabm\PHPStanBaselineAnalysis\Baseline;
use staabm\PHPStanBaselineAnalysis\BaselineAnalyzer;
use TomasVotruba\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule;
use TomasVotruba\TypeCoverage\Rules\ParamTypeCoverageRule;
use TomasVotruba\TypeCoverage\Rules\PropertyTypeCoverageRule;
use TomasVotruba\TypeCoverage\Rules\ReturnTypeCoverageRule;

class BaselineAnalyzerTest extends TestCase
{
    function testAllInComplexity():void
    {
        $analyzer = new BaselineAnalyzer(Baseline::forFile(__DIR__ . '/fixtures/all-in.neon'));
        $result = $analyzer->analyze();

        $this->assertSame(38, $result->overallErrors);
        $this->assertSame(70, $result->classesComplexity);
        $this->assertSame(2, $result->deprecations);
        $this->assertSame(5, $result->invalidPhpdocs);
        $this->assertSame(1, $result->unknownTypes);
        $this->assertSame(4, $result->anonymousVariables);
        $this->assertSame(1, $result->propertyTypeCoverage);
        $this->assertSame(27, $result->paramTypeCoverage);
        $this->assertSame(4, $result->returnTypeCoverage);
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
        $this->assertSame(0, $result->propertyTypeCoverage);
        $this->assertSame(0, $result->paramTypeCoverage);
        $this->assertSame(0, $result->returnTypeCoverage);
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
        $this->assertSame(0, $result->propertyTypeCoverage);
        $this->assertSame(0, $result->paramTypeCoverage);
        $this->assertSame(0, $result->returnTypeCoverage);
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
        $this->assertSame(0, $result->propertyTypeCoverage);
        $this->assertSame(0, $result->paramTypeCoverage);
        $this->assertSame(0, $result->returnTypeCoverage);
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
        $this->assertSame(0, $result->propertyTypeCoverage);
        $this->assertSame(0, $result->paramTypeCoverage);
        $this->assertSame(0, $result->returnTypeCoverage);
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
        $this->assertSame(0, $result->propertyTypeCoverage);
        $this->assertSame(0, $result->paramTypeCoverage);
        $this->assertSame(0, $result->returnTypeCoverage);
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
        $this->assertSame(0, $result->propertyTypeCoverage);
        $this->assertSame(0, $result->paramTypeCoverage);
        $this->assertSame(0, $result->returnTypeCoverage);
    }

    function testSeaLevels():void
    {
        $analyzer = new BaselineAnalyzer(Baseline::forFile(__DIR__ . '/fixtures/sea-level.neon'));
        $result = $analyzer->analyze();

        $this->assertSame(3, $result->overallErrors);
        $this->assertSame(0, $result->classesComplexity);
        $this->assertSame(0, $result->deprecations);
        $this->assertSame(0, $result->invalidPhpdocs);
        $this->assertSame(0, $result->unknownTypes);
        $this->assertSame(0, $result->anonymousVariables);
        $this->assertSame(1, $result->propertyTypeCoverage);
        $this->assertSame(27, $result->paramTypeCoverage);
        $this->assertSame(4, $result->returnTypeCoverage);
    }

    public function testSymplifyCompat() {
        $this->assertSame(
            BaselineAnalyzer::CLASS_COMPLEXITY_ERROR_MESSAGE,
            ClassLikeCognitiveComplexityRule::ERROR_MESSAGE
        );
        $this->assertSame(
            BaselineAnalyzer::PROPERTY_TYPE_DEClARATION_SEA_LEVEL_MESSAGE,
            PropertyTypeCoverageRule::ERROR_MESSAGE
        );
        $this->assertSame(
            BaselineAnalyzer::PARAM_TYPE_DEClARATION_SEA_LEVEL_MESSAGE,
            ParamTypeCoverageRule::ERROR_MESSAGE
        );
        $this->assertSame(
            BaselineAnalyzer::RETURN_TYPE_DEClARATION_SEA_LEVEL_MESSAGE,
            ReturnTypeCoverageRule::ERROR_MESSAGE
        );
    }

}
