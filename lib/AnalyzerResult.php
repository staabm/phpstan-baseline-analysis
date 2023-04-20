<?php

namespace staabm\PHPStanBaselineAnalysis;

final class AnalyzerResult {
    /**
     * @var \DateTimeImmutable
     */
    public $referenceDate;
    /**
     * @var int
     */
    public $overallErrors = 0;
    /**
     * @var int
     */
    public $classesComplexity = 0;

    /**
     * @var int
     */
    public $deprecations = 0;

    /**
     * @var int
     */
    public $invalidPhpdocs = 0;

    /**
     * @var int
     */
    public $unknownTypes = 0;

    /**
     * @var int
     */
    public $anonymousVariables = 0;

    /**
     * @var int
     */
    public $unusedSymbols = 0;

    /**
     * @var int<0, 100>
     */
    public $propertyTypeCoverage = 0;

    /**
     * @var int<0, 100>
     */
    public $paramTypeCoverage = 0;

    /**
     * @var int<0, 100>
     */
    public $returnTypeCoverage = 0;

}
