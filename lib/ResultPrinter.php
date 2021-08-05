<?php

namespace staabm\PHPStanBaselineAnalysis;

final class ResultPrinter {
    /**
     * @var int
     */
    public $overallComplexity = 0;

    public function printText(Baseline $baseline, AnalyzerResult $result): void
    {
        printf("Analyzing %s\n", $baseline->getFilePath());
        printf("  Overall-Complexity: %s\n", $result->overallComplexity);
    }

}