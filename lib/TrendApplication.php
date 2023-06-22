<?php

namespace staabm\PHPStanBaselineAnalysis;

use \Iterator;
use function Safe\file_get_contents;
use function Safe\json_decode;

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

    private const OUTPUT_FORMAT_JSON = 'json';

    /**
     * @param self::OUTPUT_FORMAT_* $outputFormat
     * @return self::EXIT_*
     * @throws \Safe\Exceptions\JsonException
     *
     * @throws \Safe\Exceptions\FilesystemException
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

        return $this->createOutputPlainText($reference, $comparing, $exitCode);
    }

    public function help(): void
    {
        printf('USAGE: phpstan-baseline-trend <reference-result.json> <comparing-result.json> [--format]');
    }


    /**
     * @param array<string, AnalyzerResult> $reference
     * @param array<string, AnalyzerResult> $comparing
     *
     * @param self::EXIT_* $exitCode
     */
    private function createOutputPlainText(array $reference, array $comparing, int $exitCode): int
    {
        foreach ($reference as $baselinePath => $result) {
            list($comparisonResult, $exitCode) = $this->doCompare($baselinePath, $comparing, $result, $exitCode);

            echo 'Analyzing Trend for ' . $comparisonResult->headline . "\n";
            foreach($comparisonResult->output as $key => $stats) {
                echo $key.': '.$stats['reference']." -> ".$stats['comparing']." => ".$stats['trend']."\n";
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
        foreach ($reference as $baselinePath => $result) {

            list($comparisonResult, $exitCode) = $this->doCompare($baselinePath, $comparing, $result, $exitCode);

            echo json_encode($comparisonResult);
        }

        return $exitCode;
    }

    /**
     * @param array<string, AnalyzerResult> $comparing
     *
     * @return array{ComparisonResult, int}
     */
    private function doCompare(string $baselinePath, array $comparing, AnalyzerResult $reference, int $exitCode): array
    {
        $comparison = new ComparisonResult('Analyzing Trend for ' . $baselinePath);

        if (!isset($comparing[$baselinePath])) {
            return array($comparison, $exitCode);
        }

        $exitCode = $this->compare($comparison, ResultPrinter::KEY_OVERALL_ERRORS, $reference->overallErrors, $comparing[$baselinePath]->overallErrors, $exitCode);
        $exitCode = $this->compare($comparison, ResultPrinter::KEY_CLASSES_COMPLEXITY, $reference->classesComplexity, $comparing[$baselinePath]->classesComplexity, $exitCode);
        $exitCode = $this->compare($comparison, ResultPrinter::KEY_DEPRECATIONS, $reference->deprecations, $comparing[$baselinePath]->deprecations, $exitCode);
        $exitCode = $this->compare($comparison, ResultPrinter::KEY_INVALID_PHPDOCS, $reference->invalidPhpdocs, $comparing[$baselinePath]->invalidPhpdocs, $exitCode);
        $exitCode = $this->compare($comparison, ResultPrinter::KEY_UNKNOWN_TYPES, $reference->unknownTypes, $comparing[$baselinePath]->unknownTypes, $exitCode);
        $exitCode = $this->compare($comparison, ResultPrinter::KEY_ANONYMOUS_VARIABLES, $reference->anonymousVariables, $comparing[$baselinePath]->anonymousVariables, $exitCode);
        $exitCode = $this->compare($comparison, ResultPrinter::KEY_UNUSED_SYMBOLS, $reference->unusedSymbols, $comparing[$baselinePath]->unusedSymbols, $exitCode);

        return array($comparison, $exitCode);
    }

    /**
     * @param ResultPrinter::KEY_* $key
     * @param int $referenceValue
     * @param int $comparingValue
     * @param self::EXIT_* $exitCode
     *
     * @return self::EXIT_*
     */
    private function compare(ComparisonResult $comparison, $key, $referenceValue, $comparingValue, $exitCode): int
    {
        if ($comparingValue > $referenceValue) {
            $comparison->output[$key] = [
                'reference' => $referenceValue,
                'comparing' => $comparingValue,
                'trend' => 'worse',
            ];

            $exitCode = max($exitCode, self::EXIT_WORSE);
        } elseif ($comparingValue < $referenceValue) {
            $comparison->output[$key] = [
                'reference' => $referenceValue,
                'comparing' => $comparingValue,
                'trend' => 'improved',
            ];

            $exitCode = max($exitCode, self::EXIT_IMPROVED);
        } else {
            $comparison->output[$key] = [
                'reference' => $referenceValue,
                'comparing' => $comparingValue,
                'trend' => 'good',
            ];

            $exitCode = max($exitCode, self::EXIT_STEADY);
        }

        return $exitCode;
    }
}
