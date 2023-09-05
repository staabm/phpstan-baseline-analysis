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
    public const PROPERTY_TYPE_DEClARATION_SEA_LEVEL_MESSAGE = 'Out of %d possible property types, only %d - %.1f %% actually have it. Add more property types to get over %d %%';
    /**
     * @api
     * @var string
     */
    public const PARAM_TYPE_DEClARATION_SEA_LEVEL_MESSAGE = 'Out of %d possible param types, only %d - %.1f %% actually have it. Add more param types to get over %d %%';
    /**
     * @api
     * @var string
     */
    public const RETURN_TYPE_DEClARATION_SEA_LEVEL_MESSAGE = 'Out of %d possible return types, only %d - %.1f %% actually have it. Add more return types to get over %d %%';

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
        return
            str_contains($baselineError->message, ' deprecated class ')
            || str_contains($baselineError->message, ' deprecated method ')
            || str_contains($baselineError->message, ' deprecated function ')
            || str_contains($baselineError->message, ' deprecated property ')
            ? $baselineError->count
            : 0;
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

    private function countUnusedSymbols(BaselineError $baselineError): int
    {
        return str_ends_with($baselineError->message, 'is never used$#')
            ? $baselineError->count
            : 0;
    }

    private function checkSeaLevels(AnalyzerResult $result, BaselineError $baselineError): void
    {
        if (
            sscanf(
                $baselineError->unwrapMessage(),
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
                $baselineError->unwrapMessage(),
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
                $baselineError->unwrapMessage(),
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
        return str_replace('%.1f', '', $format);
    }
}
