<?php

namespace staabm\PHPStanBaselineAnalysis;

use Safe\DateTimeImmutable;
use function Safe\preg_match;

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
    public function filter(string $filterKey): array
    {
        $result = [];

        foreach ($this->baseline->getIgnoreErrors() as $baselineError) {
            $result = $this->addErrorToResultIfFitting($filterKey, $result, $baselineError);
        }

        return $result;
    }

    /**
     * @param list<BaselineError> $result
     *
     * @return list<BaselineError>
     */
    private function addErrorToResultIfFitting(string $filterKey, array $result, BaselineError $baselineError): array
    {
        if ($filterKey === ResultPrinter::KEY_OVERALL_ERRORS) {
            $result[] = $baselineError;

            return $result;
        }

        if ($filterKey === ResultPrinter::KEY_CLASSES_COMPLEXITY && $baselineError->isComplexityError()) {
            $result[] = $baselineError;

            return $result;
        }

        if ($filterKey === ResultPrinter::KEY_DEPRECATIONS && $baselineError->isDeprecationError()) {
            $result[] = $baselineError;

            return $result;
        }

        if ($filterKey === ResultPrinter::KEY_INVALID_PHPDOCS && $baselineError->isInvalidPhpDocError()) {
            $result[] = $baselineError;

            return $result;
        }

        if ($filterKey === ResultPrinter::KEY_UNKNOWN_TYPES && $baselineError->isUnknownTypeError()) {
            $result[] = $baselineError;

            return $result;
        }

        if ($filterKey === ResultPrinter::KEY_ANONYMOUS_VARIABLES && $baselineError->isAnonymousVariableError()) {
            $result[] = $baselineError;

            return $result;
        }

        if ($filterKey === ResultPrinter::KEY_UNUSED_SYMBOLS && $baselineError->isUnusedSymbolError()) {
            $result[] = $baselineError;
        }

        return $result;
    }

}
