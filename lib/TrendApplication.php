<?php

namespace staabm\PHPStanBaselineAnalysis;

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
            list($comparisonResult, $exitCode) = $this->createComparisonResult($baselinePath, $comparing, $result, $exitCode);

            echo $comparisonResult->headline . "\n";
            foreach($comparisonResult->results as $key => $stats) {
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
        $comparisonResults = [];
        foreach ($reference as $baselinePath => $result) {

            list($comparisonResult, $exitCode) = $this->createComparisonResult($baselinePath, $comparing, $result, $exitCode);

            $comparisonResults[] = $comparisonResult;
        }

        echo json_encode($comparisonResults);


        return $exitCode;
    }

    /**
     * @param array<string, AnalyzerResult> $comparing
     * @param self::EXIT_* $exitCode
     *
     * @return array{ComparisonResult, self::EXIT_*}
     */
    private function createComparisonResult(string $baselinePath, array $comparing, AnalyzerResult $reference, int $exitCode): array
    {
        $comparisonResult = new ComparisonResult('Analyzing Trend for ' . $baselinePath);

        if (!isset($comparing[$baselinePath])) {
            return array($comparisonResult, $exitCode);
        }

        $exitCode = $this->compare($comparisonResult, ResultPrinter::KEY_OVERALL_ERRORS, $reference->overallErrors, $comparing[$baselinePath]->overallErrors, $exitCode);
        $exitCode = $this->compare($comparisonResult, ResultPrinter::KEY_CLASSES_COMPLEXITY, $reference->classesComplexity, $comparing[$baselinePath]->classesComplexity, $exitCode);
        $exitCode = $this->compare($comparisonResult, ResultPrinter::KEY_DEPRECATIONS, $reference->deprecations, $comparing[$baselinePath]->deprecations, $exitCode);
        $exitCode = $this->compare($comparisonResult, ResultPrinter::KEY_INVALID_PHPDOCS, $reference->invalidPhpdocs, $comparing[$baselinePath]->invalidPhpdocs, $exitCode);
        $exitCode = $this->compare($comparisonResult, ResultPrinter::KEY_UNKNOWN_TYPES, $reference->unknownTypes, $comparing[$baselinePath]->unknownTypes, $exitCode);
        $exitCode = $this->compare($comparisonResult, ResultPrinter::KEY_ANONYMOUS_VARIABLES, $reference->anonymousVariables, $comparing[$baselinePath]->anonymousVariables, $exitCode);
        $exitCode = $this->compare($comparisonResult, ResultPrinter::KEY_UNUSED_SYMBOLS, $reference->unusedSymbols, $comparing[$baselinePath]->unusedSymbols, $exitCode);

        return array($comparisonResult, $exitCode);
    }

    /**
     * @param ResultPrinter::KEY_* $key
     * @param int $referenceValue
     * @param int $comparingValue
     * @param self::EXIT_* $exitCode
     *
     * @return self::EXIT_*
     */
    private function compare(ComparisonResult $comparison, string $key, $referenceValue, $comparingValue, int $exitCode): int
    {
        if ($comparingValue > $referenceValue) {
            $comparison->setKey($key, $referenceValue, $comparingValue, 'worse');
            $exitCode = max($exitCode, self::EXIT_WORSE);
        } elseif ($comparingValue < $referenceValue) {
            $comparison->setKey($key, $referenceValue, $comparingValue, 'improved');
            $exitCode = max($exitCode, self::EXIT_IMPROVED);
        } else {
            $comparison->setKey($key, $referenceValue, $comparingValue, 'good');
            $exitCode = max($exitCode, self::EXIT_STEADY);
        }

        return $exitCode;
    }
}
