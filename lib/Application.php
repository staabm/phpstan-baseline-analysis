<?php

namespace staabm\PHPStanBaselineAnalysis;

final class Application
{

    public function start(string $glob): void
    {
        $baselines = BaselineFinder::forGlob($glob);

        foreach ($baselines as $baseline) {
            printf("Analyzing %s\n", $baseline->getFilePath());

            $analyzer = new BaselineAnalyzer($baseline);
            $result = $analyzer->analyze();

            printf("  Overall-Complexity: %s\n", $result->overallComplexity);
        }
    }

    public function help()
    {
        printf('USAGE: phpstan-baseline-analyze <GLOB-PATTERN>');
    }
}
