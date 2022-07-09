<?php

namespace staabm\PHPStanBaselineAnalysis;

use function Safe\preg_match;

final class BaselineAnalyzer
{
    /**
     * @var Baseline
     */
    private $baseline;

    public function __construct(Baseline $baseline)
    {
        $this->baseline = $baseline;
    }

    public function analyze(): AnalyzerResult
    {
        $result = new AnalyzerResult();

        /**
         * @var BaselineError $baselineError
         */
        foreach ($this->baseline->getIgnoreErrors() as $baselineError) {
            $this->increment('deprecations', [' deprecated class ', ' deprecated method ']);
            $this->increment('classesComplexity', ['cognitive complexity'], '/Class cognitive complexity is (?P<value>\d+), keep it under (?P<limit>\d+)/');
            $this->increment('invalidPhpdocs', ['PHPDoc tag ']);
            $this->increment('unknownTypes', [' not found'], '/Instantiated class .+ not found/');
            $this->increment('unknownTypes', ['on an unknown class', 'has invalid type unknown', 'unknown_type as its type']);
            $this->increment('anonymousVariables', ['Anonymous variable']);
        }

        return $result;
    }
}
