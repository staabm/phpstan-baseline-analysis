<?php

namespace staabm\PHPStanBaselineAnalysis;

use \Iterator;

final class TrendApplication
{
    public function start(string $referenceFilePath, string $comparingFilePath): void
    {
        $reference = $this->decodeFile($referenceFilePath);
        $comparing = $this->decodeFile($comparingFilePath);

        foreach ($reference as $baselinePath => $result) {
            echo 'Analyzing '. $baselinePath ."\n";

            if (isset($comparing[$baselinePath])) {
                printf('  %s: %d -> %d', ResultPrinter::KEY_OVERALL_CLASS_COMPLEXITY, $result->overallComplexity, $comparing[$baselinePath]->overallComplexity);
            }
        }
    }

    /**
     * @return array<string, AnalyzerResult>
     * @throws \Safe\Exceptions\FilesystemException
     * @throws \Safe\Exceptions\JsonException
     */
    private function decodeFile(string $filePath):array {
        $content = \Safe\file_get_contents($filePath);
        $json = \Safe\json_decode($content, true);

        $decoded = [];
        foreach($json as $data) {

            if (!is_array($data)) {
                throw new \RuntimeException('Expecting array, got '. gettype($data));
            }

            foreach($data as $baselinePath => $resultArray) {

                if (!is_string($baselinePath)) {
                    throw new \RuntimeException('Expecting string, got '. gettype($baselinePath));
                }
                if (!is_array($resultArray)) {
                    throw new \RuntimeException('Expecting string, got '. gettype($resultArray));
                }

                $result = new AnalyzerResult();
                $result->overallComplexity = $resultArray[ResultPrinter::KEY_OVERALL_CLASS_COMPLEXITY];

                $decoded[$baselinePath] = $result;
            }
        }

        return $decoded;
    }
}