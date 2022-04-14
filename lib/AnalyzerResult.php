<?php

namespace staabm\PHPStanBaselineAnalysis;

final class AnalyzerResult {
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
}