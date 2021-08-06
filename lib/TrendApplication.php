<?php

namespace staabm\PHPStanBaselineAnalysis;

use \Iterator;

final class TrendApplication
{
    const EXIT_IMPROVED = 0;
    const EXIT_STEADY = 1;
    const EXIT_WORSE = 2;

    /**
     * @return self::EXIT_*
     * @throws \Safe\Exceptions\JsonException
     *
     * @throws \Safe\Exceptions\FilesystemException
     */
    public function start(string $referenceFilePath, string $comparingFilePath): int
    {
        $exitCode = self::EXIT_IMPROVED;

        $reference = $this->decodeFile($referenceFilePath);
        $comparing = $this->decodeFile($comparingFilePath);

        foreach ($reference as $baselinePath => $result) {
            echo 'Analyzing Trend for ' . $baselinePath . "\n";

            if (isset($comparing[$baselinePath])) {
                $exitCode = $this->compare(ResultPrinter::KEY_OVERALL_CLASS_COMPLEXITY, $result->classesComplexity, $comparing[$baselinePath]->classesComplexity, $exitCode);
                echo "\n";

                $exitCode = $this->compare(ResultPrinter::KEY_DEPRECATIONS, $result->deprecations, $comparing[$baselinePath]->deprecations, $exitCode);
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
                if (array_key_exists(ResultPrinter::KEY_OVERALL_CLASS_COMPLEXITY, $resultArray)) {
                    $result->classesComplexity = $resultArray[ResultPrinter::KEY_OVERALL_CLASS_COMPLEXITY];
                }
                if (array_key_exists(ResultPrinter::KEY_DEPRECATIONS, $resultArray)) {
                    $result->deprecations = $resultArray[ResultPrinter::KEY_DEPRECATIONS];
                }

                $decoded[$baselinePath] = $result;
            }
        }

        return $decoded;
    }

    /**
     * @param ResultPrinter::KEY_* $key
     * @param int $referenceValue
     * @param int $comparingValue
     * @param self::EXIT_* $exitCode
     *
     * @return self::EXIT_*
     */
    private function compare($key, $referenceValue, $comparingValue, $exitCode): int
    {
        if ($comparingValue > $referenceValue) {
            printf('  %s: %d -> %d => worse', $key, $referenceValue, $comparingValue);

            $exitCode = max($exitCode, self::EXIT_WORSE);
        } elseif ($comparingValue < $referenceValue) {
            printf('  %s: %d -> %d => improved', $key, $referenceValue, $comparingValue);

            $exitCode = max($exitCode, self::EXIT_IMPROVED);
        } else {
            printf('  %s: %d -> %d => good', $key, $referenceValue, $comparingValue);

            $exitCode = max($exitCode, self::EXIT_STEADY);
        }
        return $exitCode;
    }
}