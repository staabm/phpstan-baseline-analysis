<?php

namespace staabm\PHPStanBaselineAnalysis;

use function Safe\json_encode;

final class TrendApplication
{
    /**
     * @api
     */
    const EXIT_IMPROVED = 0;
    /**
     * @api
     */
    const EXIT_STEADY = 1;
    /**
     * @api
     */
    const EXIT_WORSE = 2;

    public const OUTPUT_FORMAT_DEFAULT = 'text';

    /**
     * @api
     */
    public const OUTPUT_FORMAT_JSON = 'json';

    /**
     * @return list<self::OUTPUT_FORMAT_*>
     */
    public static function getAllowedOutputFormats(): array
    {
        return [
            self::OUTPUT_FORMAT_DEFAULT,
            self::OUTPUT_FORMAT_JSON,
        ];
    }

    /**
     * @param self::OUTPUT_FORMAT_* $outputFormat
     * @return self::EXIT_*
     * @throws \Safe\Exceptions\JsonException
     *
     * @throws \Safe\Exceptions\FilesystemException
     *
     * @api
     */
    public function start(string $referenceFilePath, string $comparingFilePath, string $outputFormat): int
    {
        $exitCode = self::EXIT_IMPROVED;

        $reader = new AnalyzerResultReader();
        $reference = $reader->readFile($referenceFilePath);
        $comparing = $reader->readFile($comparingFilePath);

        if ($outputFormat === self::OUTPUT_FORMAT_JSON) {
            return $this->createOutputJson($reference, $comparing, $exitCode);
        }

        return $this->createOutputText($reference, $comparing, $exitCode);
    }

    /**
     * @api
     */
    public function help(): void
    {
        printf('USAGE: phpstan-baseline-trend <reference-result.json> <comparing-result.json> [--format=json|text]');
    }


    /**
     * @param array<string, AnalyzerResult> $reference
     * @param array<string, AnalyzerResult> $comparing
     * @param self::EXIT_* $exitCode
     *
     * @return self::EXIT_*
     */
    private function createOutputText(array $reference, array $comparing, int $exitCode): int
    {
        foreach ($reference as $baselinePath => $result) {
            list($trendResult, $exitCode) = $this->createTrendResult($baselinePath, $comparing, $result, $exitCode);

            echo $trendResult->headline . "\n";
            foreach($trendResult->results as $key => $stats) {
                echo '  '.$key.': '.$stats['reference']." -> ".$stats['comparing']." => ".$stats['trend']."\n";
            }
        }

        return $exitCode;
    }

    /**
     * @param array<string, AnalyzerResult> $reference
     * @param array<string, AnalyzerResult> $comparing
     * @param self::EXIT_* $exitCode
     *
     * @return self::EXIT_*
     */
    private function createOutputJson(array $reference, array $comparing, int $exitCode): int
    {
        $trendResults = [];
        foreach ($reference as $baselinePath => $result) {

            list($trendResult, $exitCode) = $this->createTrendResult($baselinePath, $comparing, $result, $exitCode);

            $trendResults[] = $trendResult;
        }

        echo json_encode($trendResults);


        return $exitCode;
    }

    /**
     * @param array<string, AnalyzerResult> $comparing
     * @param self::EXIT_* $exitCode
     *
     * @return array{TrendResult, self::EXIT_*}
     */
    private function createTrendResult(string $baselinePath, array $comparing, AnalyzerResult $reference, int $exitCode): array
    {
        $trendResult = new TrendResult('Analyzing Trend for ' . $baselinePath);

        if (!isset($comparing[$baselinePath])) {
            return array($trendResult, $exitCode);
        }

        // decreased trends are better
        $exitCode = $this->compareDecreasing($trendResult, ResultPrinter::KEY_OVERALL_ERRORS, $reference->overallErrors, $comparing[$baselinePath]->overallErrors, $exitCode);
        $exitCode = $this->compareDecreasing($trendResult, ResultPrinter::KEY_CLASSES_COMPLEXITY, $reference->classesComplexity, $comparing[$baselinePath]->classesComplexity, $exitCode);
        $exitCode = $this->compareDecreasing($trendResult, ResultPrinter::KEY_DEPRECATIONS, $reference->deprecations, $comparing[$baselinePath]->deprecations, $exitCode);
        $exitCode = $this->compareDecreasing($trendResult, ResultPrinter::KEY_INVALID_PHPDOCS, $reference->invalidPhpdocs, $comparing[$baselinePath]->invalidPhpdocs, $exitCode);
        $exitCode = $this->compareDecreasing($trendResult, ResultPrinter::KEY_UNKNOWN_TYPES, $reference->unknownTypes, $comparing[$baselinePath]->unknownTypes, $exitCode);
        $exitCode = $this->compareDecreasing($trendResult, ResultPrinter::KEY_ANONYMOUS_VARIABLES, $reference->anonymousVariables, $comparing[$baselinePath]->anonymousVariables, $exitCode);
        $exitCode = $this->compareDecreasing($trendResult, ResultPrinter::KEY_UNUSED_SYMBOLS, $reference->unusedSymbols, $comparing[$baselinePath]->unusedSymbols, $exitCode);

        // increased trends are better
        $exitCode = $this->compareIncreasing($trendResult, ResultPrinter::KEY_RETURN_TYPE_COVERAGE, $reference->returnTypeCoverage, $comparing[$baselinePath]->returnTypeCoverage, $exitCode);
        $exitCode = $this->compareIncreasing($trendResult, ResultPrinter::KEY_PROPERTY_TYPE_COVERAGE, $reference->propertyTypeCoverage, $comparing[$baselinePath]->propertyTypeCoverage, $exitCode);
        $exitCode = $this->compareIncreasing($trendResult, ResultPrinter::KEY_PARAM_TYPE_COVERAGE, $reference->paramTypeCoverage, $comparing[$baselinePath]->paramTypeCoverage, $exitCode);

        return array($trendResult, $exitCode);
    }

    /**
     * @param ResultPrinter::KEY_* $key
     * @param int $referenceValue
     * @param int $comparingValue
     * @param self::EXIT_* $exitCode
     *
     * @return self::EXIT_*
     */
    private function compareDecreasing(TrendResult $trendResult, string $key, $referenceValue, $comparingValue, int $exitCode): int
    {
        if ($comparingValue > $referenceValue) {
            $trendResult->setKey($key, $referenceValue, $comparingValue, 'worse');
            $exitCode = max($exitCode, self::EXIT_WORSE);
        } elseif ($comparingValue < $referenceValue) {
            $trendResult->setKey($key, $referenceValue, $comparingValue, 'improved');
            $exitCode = max($exitCode, self::EXIT_IMPROVED);
        } else {
            $trendResult->setKey($key, $referenceValue, $comparingValue, 'good');
            $exitCode = max($exitCode, self::EXIT_STEADY);
        }

        return $exitCode;
    }

    /**
     * @param ResultPrinter::KEY_* $key
     * @param int $referenceValue
     * @param int $comparingValue
     * @param self::EXIT_* $exitCode
     *
     * @return self::EXIT_*
     */
    private function compareIncreasing(TrendResult $trendResult, string $key, $referenceValue, $comparingValue, int $exitCode): int
    {
        if ($comparingValue > $referenceValue) {
            $trendResult->setKey($key, $referenceValue, $comparingValue, 'improved');
            $exitCode = max($exitCode, self::EXIT_IMPROVED);
        } elseif ($comparingValue < $referenceValue) {
            $trendResult->setKey($key, $referenceValue, $comparingValue, 'worse');
            $exitCode = max($exitCode, self::EXIT_WORSE);
        } else {
            $trendResult->setKey($key, $referenceValue, $comparingValue, 'good');
            $exitCode = max($exitCode, self::EXIT_STEADY);
        }

        return $exitCode;
    }
}
