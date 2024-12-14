<?php

namespace staabm\PHPStanBaselineAnalysis;

final class BaselineFilter
{
    private Baseline $baseline;

    public function __construct(Baseline $baseline)
    {
        $this->baseline = $baseline;
    }

    /**
     * @return list<BaselineError>
     */
    public function filter(FilterConfig $filterConfig): array
    {
        $result = [];

        foreach ($this->baseline->getIgnoreErrors() as $baselineError) {
            $result = $this->addErrorToResultIfFitting($filterConfig, $result, $baselineError);
        }

        return $result;
    }

    /**
     * @param list<BaselineError> $result
     *
     * @return list<BaselineError>
     */
    private function addErrorToResultIfFitting(FilterConfig $filterConfig, array $result, BaselineError $baselineError): array
    {
        $matched = $this->matchedFilter($filterConfig, $baselineError);

        if ($filterConfig->isExcluding()) {
            if (!$matched) {
                $result[] = $baselineError;
            }
        } else {
            if ($matched) {
                $result[] = $baselineError;
            }
        }

        return $result;
    }

    private function matchedFilter(FilterConfig $filterConfig, BaselineError $baselineError): bool
    {
        $matched = false;
        if ($filterConfig->containsKey(ResultPrinter::KEY_CLASSES_COMPLEXITY)
            && $baselineError->isComplexityError()) {
            $matched = true;
        }

        if ($filterConfig->containsKey(ResultPrinter::KEY_DEPRECATIONS)
            && $baselineError->isDeprecationError()) {
            $matched = true;
        }

        if (
            $filterConfig->containsKey(ResultPrinter::KEY_INVALID_PHPDOCS)
            && $baselineError->isInvalidPhpDocError()) {
            $matched = true;
        }

        if ($filterConfig->containsKey(ResultPrinter::KEY_UNKNOWN_TYPES)
            && $baselineError->isUnknownTypeError()) {
            $matched = true;
        }

        if ($filterConfig->containsKey(ResultPrinter::KEY_ANONYMOUS_VARIABLES)
            && $baselineError->isAnonymousVariableError()) {
            $matched = true;
        }

        if ($filterConfig->containsKey(ResultPrinter::KEY_UNUSED_SYMBOLS)
            && $baselineError->isUnusedSymbolError()) {
            $matched = true;
        }

        return $matched;
    }

}
