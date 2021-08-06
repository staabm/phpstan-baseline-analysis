<?php

namespace staabm\PHPStanBaselineAnalysis;

use \Iterator;
use function Safe\sprintf;
use function Safe\json_encode;

final class ResultPrinter {
    const FORMAT_JSON = 'json';
    const FORMAT_TEXT = 'text';

    const KEY_OVERALL_CLASS_COMPLEXITY = 'Overall-Class-Cognitive-Complexity';

    /**
     * @var int
     */
    public $overallComplexity = 0;

    /**
     * @return Iterator<string>
     */
    public function streamText(Baseline $baseline, AnalyzerResult $result): Iterator
    {
        yield sprintf("Analyzing %s\n", $baseline->getFilePath());
        yield sprintf("  %s: %s\n", self::KEY_OVERALL_CLASS_COMPLEXITY, $result->classesComplexity);
    }

    /**
     * @return Iterator<string>
     */
    public function streamJson(Baseline $baseline, AnalyzerResult $result): Iterator
    {
        yield json_encode([
            $baseline->getFilePath() => [
                self::KEY_OVERALL_CLASS_COMPLEXITY => $result->classesComplexity
            ]
        ]);
    }
}