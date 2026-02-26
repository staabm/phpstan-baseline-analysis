<?php

namespace staabm\PHPStanBaselineAnalysis;

use \Iterator;

final class AnalyzeApplication
{
    /**
     * @api
     */
    const EXIT_SUCCESS = 0;
    /**
     * @api
     */
    const EXIT_ERROR = 1;

    /**
     * @param ResultPrinter::FORMAT_* $format
     *
     * @return self::EXIT_*
     *
     * @api
     */
    public function start(string $glob, string $format): int
    {
        $printer = new ResultPrinter();
        $baselines = BaselineFinder::forGlob($glob);
        $numBaselines = count($baselines);

        for($i = 0; $i < $numBaselines; $i++) {
            $baseline = $baselines[$i];
            $isFirst = $i == 0;
            $isLast = $i == $numBaselines - 1;

            $analyzer = new BaselineAnalyzer($baseline);
            $result = $analyzer->analyze();

            if ($format == ResultPrinter::FORMAT_JSON) {
                $stream = $printer->streamJson($baseline, $result);
            } else {
                $stream = $printer->streamText($baseline, $result);
            }

            $this->printResult($format, $isFirst, $isLast, $stream);
        }

        if ($numBaselines > 0) {
            return self::EXIT_SUCCESS;
        }
        return self::EXIT_ERROR;
    }

    public function summarize(string $glob, string $format): int
    {
        $baselines = BaselineFinder::forGlob($glob);
        $numBaselines = count($baselines);
        if ($numBaselines === 0) {
            return self::EXIT_ERROR;
        }

        $resultSummary = new AnalyzerResult();
        $baselineSummary = new Baseline();

        foreach ($baselines as $baseline) {
            $analyzer = new BaselineAnalyzer($baseline);
            $result = $analyzer->analyze();
            $resultSummary->overallErrors += $result->overallErrors;
            $resultSummary->deprecations += $result->deprecations;
            $resultSummary->invalidPhpdocs += $result->invalidPhpdocs;
            $resultSummary->unknownTypes += $result->unknownTypes;
            $resultSummary->anonymousVariables += $result->anonymousVariables;
            $resultSummary->unusedSymbols += $result->unusedSymbols;
        }

        $printer = new ResultPrinter();

        if ($format == ResultPrinter::FORMAT_JSON) {
            $stream = $printer->streamJson($baselineSummary, $resultSummary);
        } else {
            $stream = $printer->streamText($baselineSummary, $resultSummary);
        }

        $this->printSummary($format, $stream);

        return self::EXIT_SUCCESS;
    }

    /**
     * @api
     */
    public function help(): void
    {
        printf('USAGE: phpstan-baseline-analyze <GLOB-PATTERN>');
    }

    /**
     * @param Iterator<string> $stream
     */
    private function printResult(string $format, bool $isFirst, bool $isLast, Iterator $stream): void
    {
        if ($format == ResultPrinter::FORMAT_JSON) {
            if ($isFirst) {
                printf('[');
            }
        }

        foreach ($stream as $string) {
            printf($string);

            if ($format == ResultPrinter::FORMAT_JSON && !$isLast) {
                printf(",\n");
            }
        }

        if ($format == ResultPrinter::FORMAT_JSON) {
            if ($isLast) {
                printf(']');
            }
        }
    }
}
