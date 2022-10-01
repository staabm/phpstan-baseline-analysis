<?php

namespace staabm\PHPStanBaselineAnalysis;

use \Iterator;
use Safe\DateTimeImmutable;
use function Safe\sprintf;
use function Safe\json_encode;

final class ResultPrinter {
    const DATE_FORMAT = DateTimeImmutable::RFC2822;

    const FORMAT_JSON = 'json';
    const FORMAT_TEXT = 'text';

    // use plural string-values
    const KEY_REFERENCE_DATE = 'Date';
    const KEY_OVERALL_ERRORS = 'Overall-Errors';
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
        $referenceDate = '';
        if ($result->referenceDate !== null) {
            $referenceDate = $result->referenceDate->format(ResultPrinter::DATE_FORMAT);
        }
        yield sprintf("Analyzing %s\n", $baseline->getFilePath());
        yield sprintf("  %s: %s\n", self::KEY_REFERENCE_DATE, $referenceDate);
        yield sprintf("  %s: %s\n", self::KEY_OVERALL_ERRORS, $result->overallErrors);
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
        $referenceDate = null;
        if ($result->referenceDate !== null) {
            $referenceDate = $result->referenceDate->format(ResultPrinter::DATE_FORMAT);
        }

        yield json_encode([
            $baseline->getFilePath() => [
                self::KEY_REFERENCE_DATE => $referenceDate,
                self::KEY_OVERALL_ERRORS => $result->overallErrors,
                self::KEY_CLASSES_COMPLEXITY => $result->classesComplexity,
                self::KEY_DEPRECATIONS => $result->deprecations,
                self::KEY_INVALID_PHPDOCS => $result->invalidPhpdocs,
                self::KEY_UNKNOWN_TYPES => $result->unknownTypes,
                self::KEY_ANONYMOUS_VARIABLES => $result->anonymousVariables,
            ]
        ]);
    }
}