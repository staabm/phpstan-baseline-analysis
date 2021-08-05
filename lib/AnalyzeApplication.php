<?php

namespace staabm\PHPStanBaselineAnalysis;

use \Iterator;

final class AnalyzeApplication
{

    /**
     * @param ResultPrinter::FORMAT_* $format
     * @throws \Safe\Exceptions\FilesystemException
     */
    public function start(string $glob, string $format): int
    {
        $printer = new ResultPrinter();
        $baselines = BaselineFinder::forGlob($glob);
        $numBaselines = count ($baselines);

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

        return 0;
    }

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
