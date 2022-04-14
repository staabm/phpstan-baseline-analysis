<?php

namespace staabm\PHPStanBaselineAnalysis;

use \Iterator;
use function Safe\sprintf;
use function Safe\json_encode;

final class ResultPrinter {
    const FORMAT_JSON = 'json';
    const FORMAT_TEXT = 'text';

    // use plural string-values
    const KEY_CLASSES_COMPLEXITY = 'Classes-Cognitive-Complexity';
    const KEY_DEPRECATIONS = 'Deprecations';
    const KEY_INVALID_PHPDOCS = 'Invalid-Phpdocs';
    const KEY_UNKNOWN_TYPES = 'Unknown-Types';
    const KEY_ANONYMOUS_VARIABLES = 'Anonymous-Variables';

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
        yield sprintf("  %s: %s\n", self::KEY_CLASSES_COMPLEXITY, $result->classesComplexity);
        yield sprintf("  %s: %s\n", self::KEY_DEPRECATIONS, $result->deprecations);
        yield sprintf("  %s: %s\n", self::KEY_INVALID_PHPDOCS, $result->invalidPhpdocs);
        yield sprintf("  %s: %s\n", self::KEY_UNKNOWN_TYPES, $result->unknownTypes);
        yield sprintf("  %s: %s\n", self::KEY_ANONYMOUS_VARIABLES, $result->anonymousVariables);
    }

    /**
     * @return Iterator<string>
     */
    public function streamJson(Baseline $baseline, AnalyzerResult $result): Iterator
    {
        yield json_encode([
            $baseline->getFilePath() => [
                self::KEY_CLASSES_COMPLEXITY => $result->classesComplexity,
                self::KEY_DEPRECATIONS => $result->deprecations,
                self::KEY_INVALID_PHPDOCS => $result->invalidPhpdocs,
                self::KEY_UNKNOWN_TYPES => $result->unknownTypes,
                self::KEY_ANONYMOUS_VARIABLES => $result->anonymousVariables,
            ]
        ]);
    }
}