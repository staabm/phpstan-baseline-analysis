<?php

namespace staabm\PHPStanBaselineAnalysis;

use \Iterator;

final class TrendApplication
{
    const EXIT_IMPROVED = 0;
    const EXIT_STEADY = 1;
    const EXIT_WORSE = 2;

    /**
     * @throws \Safe\Exceptions\FilesystemException
     * @throws \Safe\Exceptions\JsonException
     *
     * @return self::EXIT_*
     */
    public function start(string $referenceFilePath, string $comparingFilePath): int
    {
        $exitCode = self::EXIT_IMPROVED;

        $reference = $this->decodeFile($referenceFilePath);
        $comparing = $this->decodeFile($comparingFilePath);

        foreach ($reference as $baselinePath => $result) {
            echo 'Analyzing Trend for ' . $baselinePath . "\n";

            if (isset($comparing[$baselinePath])) {
                if ($comparing[$baselinePath]->overallComplexity > $result->overallComplexity) {
                    printf('  %s: %d -> %d => worse', ResultPrinter::KEY_OVERALL_CLASS_COMPLEXITY, $result->overallComplexity, $comparing[$baselinePath]->overallComplexity);

                    $exitCode = max($exitCode, self::EXIT_WORSE);
                } elseif ($comparing[$baselinePath]->overallComplexity < $result->overallComplexity) {
                    printf('  %s: %d -> %d => improved', ResultPrinter::KEY_OVERALL_CLASS_COMPLEXITY, $result->overallComplexity, $comparing[$baselinePath]->overallComplexity);

                    $exitCode = max($exitCode, self::EXIT_IMPROVED);
                } else {
                    printf('  %s: %d -> %d => good', ResultPrinter::KEY_OVERALL_CLASS_COMPLEXITY, $result->overallComplexity, $comparing[$baselinePath]->overallComplexity);

                    $exitCode = max($exitCode, self::EXIT_STEADY);
                }

                echo "\n";
            }
        }

        return $exitCode;
    }

    public function help(): void
    {
        printf('USAGE: phpstan-baseline-trend <reference-result.json> <comparing-result.json>');
    }

    /**
     * @return array<string, AnalyzerResult>
     * @throws \Safe\Exceptions\FilesystemException
     * @throws \Safe\Exceptions\JsonException
     */
    private function decodeFile(string $filePath): array
    {
        $content = \Safe\file_get_contents($filePath);
        $json = \Safe\json_decode($content, true);

        if (!is_array($json)) {
            throw new \RuntimeException('Expecting array, got ' . gettype($json));
        }

        $decoded = [];
        foreach ($json as $data) {

            if (!is_array($data)) {
                throw new \RuntimeException('Expecting array, got ' . gettype($data));
            }

            foreach ($data as $baselinePath => $resultArray) {

                if (!is_string($baselinePath)) {
                    throw new \RuntimeException('Expecting string, got ' . gettype($baselinePath));
                }
                if (!is_array($resultArray)) {
                    throw new \RuntimeException('Expecting string, got ' . gettype($resultArray));
                }

                $result = new AnalyzerResult();
                $result->overallComplexity = $resultArray[ResultPrinter::KEY_OVERALL_CLASS_COMPLEXITY];

                $decoded[$baselinePath] = $result;
            }
        }

        return $decoded;
    }
}