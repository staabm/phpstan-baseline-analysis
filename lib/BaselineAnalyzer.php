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
            $errorMessage = $baselineError->message;

            if (str_contains($errorMessage, ' deprecated class ') || str_contains($errorMessage, ' deprecated method ')) {
                $result->deprecations += $baselineError->count;
            } elseif (str_contains($errorMessage, 'cognitive complexity')) {
                preg_match('/Class cognitive complexity is (?P<value>\d+), keep it under (?P<limit>\d+)/', $errorMessage, $matches);
                if ($matches) {
                    $result->classesComplexity += ($matches['value'] * $baselineError->count);
                }
            } elseif (str_contains($errorMessage, 'PHPDoc tag ')) {
                $result->invalidPhpdocs += $baselineError->count;
            }
        }

        return $result;
    }
}