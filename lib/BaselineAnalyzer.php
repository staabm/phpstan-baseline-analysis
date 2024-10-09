<?php

namespace staabm\PHPStanBaselineAnalysis;

use Safe\DateTimeImmutable;
use function Safe\preg_match;

final class BaselineAnalyzer
{
    /**
     * @api
     * @var string
     */
    public const CLASS_COMPLEXITY_ERROR_MESSAGE = 'Class cognitive complexity is %d, keep it under %d';
    /**
     * @api
     * @var string
     */
    public const PROPERTY_TYPE_DEClARATION_SEA_LEVEL_MESSAGE = 'Out of %d possible property types, only %d - %.1f %% actually have it. Add more property types to get over %s %%';
    /**
     * @api
     * @var string
     */
    public const PARAM_TYPE_DEClARATION_SEA_LEVEL_MESSAGE = 'Out of %d possible param types, only %d - %.1f %% actually have it. Add more param types to get over %s %%';
    /**
     * @api
     * @var string
     */
    public const RETURN_TYPE_DEClARATION_SEA_LEVEL_MESSAGE = 'Out of %d possible return types, only %d - %.1f %% actually have it. Add more return types to get over %s %%';

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
            // accumulating errors
            $result->overallErrors += $baselineError->count;
            $result->deprecations += $this->countDeprecations($baselineError);
            $result->classesComplexity += $this->countClassesComplexity($baselineError);
            $result->invalidPhpdocs += $this->countInvalidPhpdocs($baselineError);
            $result->unknownTypes += $this->countUnknownTypes($baselineError);
            $result->anonymousVariables += $this->countAnonymousVariables($baselineError);
            $result->unusedSymbols += $this->countUnusedSymbols($baselineError);

            // project wide errors, only reported once per baseline
            $this->checkSeaLevels($result, $baselineError);
        }

        return $result;
    }

    private function countDeprecations(BaselineError $baselineError): int
    {
        return $baselineError->isDeprecationError() ? $baselineError->count : 0;
    }

    private function countClassesComplexity(BaselineError $baselineError): int
    {
        if (sscanf($baselineError->unwrapMessage(), self::CLASS_COMPLEXITY_ERROR_MESSAGE, $value, $limit) > 0) {
            return (int)$value * $baselineError->count;
        }
        return 0;
    }

    private function countInvalidPhpdocs(BaselineError $baselineError): int
    {
        return $baselineError->isInvalidPhpDocError()
            ? $baselineError->count
            : 0;
    }

    private function countUnknownTypes(BaselineError $baselineError): int
    {
        return $baselineError->isUnknownTypeError()
            ? $baselineError->count
            : 0;
    }

    private function countAnonymousVariables(BaselineError $baselineError): int
    {
        return $baselineError->isAnonymousVariableError()
            ? $baselineError->count
            : 0;
    }

    private function countUnusedSymbols(BaselineError $baselineError): int
    {
        return $baselineError->isUnusedSymbolError()
            ? $baselineError->count
            : 0;
    }

    private function checkSeaLevels(AnalyzerResult $result, BaselineError $baselineError): void
    {
        if (
            sscanf(
                $this->normalizeMessage($baselineError),
                $this->printfToScanfFormat(self::PROPERTY_TYPE_DEClARATION_SEA_LEVEL_MESSAGE),
                $absoluteCountMin, $coveragePercent, $goalPercent) >= 2
        ) {
            if (!is_int($coveragePercent) || $coveragePercent < 0 || $coveragePercent > 100) {
                throw new \LogicException('Invalid property coveragePercent: '. $coveragePercent);
            }
            $result->propertyTypeCoverage = $coveragePercent;
        }

        if (
            sscanf(
                $this->normalizeMessage($baselineError),
                $this->printfToScanfFormat(self::PARAM_TYPE_DEClARATION_SEA_LEVEL_MESSAGE),
                $absoluteCountMin, $coveragePercent, $goalPercent) >= 2
        ) {
            if (!is_int($coveragePercent) || $coveragePercent < 0 || $coveragePercent > 100) {
                throw new \LogicException('Invalid parameter coveragePercent: '. $coveragePercent);
            }
            $result->paramTypeCoverage = $coveragePercent;
        }

        if (
            sscanf(
                $this->normalizeMessage($baselineError),
                $this->printfToScanfFormat(self::RETURN_TYPE_DEClARATION_SEA_LEVEL_MESSAGE),
                $absoluteCountMin, $coveragePercent, $goalPercent) >= 2
        ) {
            if (!is_int($coveragePercent) || $coveragePercent < 0 || $coveragePercent > 100) {
                throw new \LogicException('Invalid return coveragePercent: '. $coveragePercent);
            }
            $result->returnTypeCoverage = $coveragePercent;
        }
    }

    private function printfToScanfFormat(string $format): string {
        // we don't need the float value, therefore simply ignore it, to make the format parseable by sscanf
        // see https://github.com/php/php-src/issues/12126
        // additionally this makes the output format of tomasvotruba/type-coverage 0.2.* compatible with tomasvotruba/type-coverage 0.1.*
        return str_replace('%d - %.1f', '%d', $format);
    }

    private function normalizeMessage(BaselineError $baselineError): string {
        // makes the message format of tomasvotruba/type-coverage 0.2.* compatible with tomasvotruba/type-coverage 0.1.*
        return \Safe\preg_replace('/only \d+ \- (\d+).\d %/', 'only $1 %', $baselineError->unwrapMessage());
    }
}
