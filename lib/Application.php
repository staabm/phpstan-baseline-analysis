<?php

namespace staabm\PHPStanBaselineAnalysis;

use Nette\Neon\Neon;

final class Application {

    public function start(string $glob)
    {
        $baselines = BaselineFinder::forGlob($glob);

        foreach($baselines as $baseline) {
            printf("Analyzing %s\n", $baseline->getFilePath());

            $analyzer = new BaselineAnalyzer($baseline);
            $result = $analyzer->analyze();

            printf("  Overall-Complexity: %s\n", $result->overallComplexity);
        }
    }
}
