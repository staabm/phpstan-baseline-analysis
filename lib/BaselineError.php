<?php

namespace staabm\PHPStanBaselineAnalysis;

use function Safe\preg_match;

/**
 * @immutable
 */
final class BaselineError
{
    public int $count;

    public string $message;

    public string $path;

    public function __construct(int $count, string $message, string $path)
    {
        $this->count = $count;
        $this->message = $message;
        $this->path = $path;
    }

    /**
     * Returns the baseline error message, without regex delimiters.
     * Note: the message may still contain escaped regex meta characters.
     */
    public function unwrapMessage(): string {
        $msg = $this->message;
        $msg = str_replace(['\\-', '\\.', '%%'], ['-', '.', '%'], $msg);
        $msg = trim($msg, '#^$');
        return $msg;
    }

    public function isDeprecationError(): bool
    {
        return str_contains($this->message, ' deprecated class ')
            || str_contains($this->message, ' deprecated method ')
            || str_contains($this->message, ' deprecated function ')
            || str_contains($this->message, ' deprecated property ');
    }

    public function isComplexityError(): bool
    {
        return sscanf($this->unwrapMessage(), BaselineAnalyzer::CLASS_COMPLEXITY_ERROR_MESSAGE, $value, $limit) > 0;
    }

    public function isInvalidPhpDocError(): bool
    {
        return str_contains($this->message, 'PHPDoc tag ');
    }

    public function isUnknownTypeError(): bool
    {
        return preg_match('/Instantiated class .+ not found/', $this->message, $matches) === 1
            || str_contains($this->message, 'on an unknown class')
            || str_contains($this->message, 'has invalid type unknown')
            || str_contains($this->message, 'unknown_type as its type');
    }

    public function isAnonymousVariableError(): bool
    {
        return str_contains($this->message, 'Anonymous variable');
    }

    public function isUnusedSymbolError(): bool
    {
        return str_ends_with($this->message, 'is never used$#');
    }
}
