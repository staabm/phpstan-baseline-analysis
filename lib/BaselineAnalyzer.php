<?php

namespace staabm\PHPStanBaselineAnalysis;

use Safe\DateTimeImmutable;
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
        $result->referenceDate = new DateTimeImmutable();

        /**
         * @var BaselineError $baselineError
         */
        foreach ($this->baseline->getIgnoreErrors() as $baselineError) {
            $result->overallErrors += $baselineError->count;
            $result->deprecations += $this->countDeprecations($baselineError);
            $result->classesComplexity += $this->countClassesComplexity($baselineError);
            $result->invalidPhpdocs += $this->countInvalidPhpdocs($baselineError);
            $result->unknownTypes += $this->countUnknownTypes($baselineError);
            $result->anonymousVariables += $this->countAnonymousVariables($baselineError);
        }

        return $result;
    }

    private function countDeprecations(BaselineError $baselineError): int
    {
        return str_contains($baselineError->message, ' deprecated class ') || str_contains($baselineError->message, ' deprecated method ')
            ? $baselineError->count
            : 0;
    }

    private function countClassesComplexity(BaselineError $baselineError): int
    {
        return preg_match('/Class cognitive complexity is (?P<value>\d+), keep it under (?P<limit>\d+)/', $baselineError->message, $matches) === 1
            ? (int)$matches['value'] * $baselineError->count
            : 0;
    }

    private function countInvalidPhpdocs(BaselineError $baselineError): int
    {
        return str_contains($baselineError->message, 'PHPDoc tag ')
            ? $baselineError->count
            : 0;
    }

    private function countUnknownTypes(BaselineError $baselineError): int
    {
        $notFoundCount = preg_match('/Instantiated class .+ not found/', $baselineError->message, $matches) === 1
            ? $baselineError->count
            : 0;

        $unknownCount = str_contains($baselineError->message, 'on an unknown class') || str_contains($baselineError->message, 'has invalid type unknown') || str_contains($baselineError->message, 'unknown_type as its type')
            ? $baselineError->count
            : 0;

        return $notFoundCount + $unknownCount;
    }

    private function countAnonymousVariables(BaselineError $baselineError): int
    {
        return str_contains($baselineError->message, 'Anonymous variable')
            ? $baselineError->count
            : 0;
    }
}
