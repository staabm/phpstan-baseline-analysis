<?php

namespace staabm\PHPStanBaselineAnalysis;

final class Application
{

    public function start(string $glob): void
    {
        $printer = new ResultPrinter();
        $baselines = BaselineFinder::forGlob($glob);

        foreach ($baselines as $baseline) {
            $analyzer = new BaselineAnalyzer($baseline);
            $result = $analyzer->analyze();

            $printer->printText($baseline, $result);
        }
    }

    public function help(): void
    {
        printf('USAGE: phpstan-baseline-analyze <GLOB-PATTERN>');
    }
}
