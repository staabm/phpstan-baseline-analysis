<?php

namespace staabm\PHPStanBaselineAnalysis;

/**
 * @immutable
 */
final class BaselineError
{
    public int $count;

    public ?string $message;

    public ?string $rawMessage;

    public ?string $path;

    public ?string $identifier;

    public function __construct(int $count, ?string $message, ?string $path, ?string $identifier, ?string $rawMessage)
    {
        $this->count = $count;
        $this->message = $message;
        $this->rawMessage = $rawMessage;
        $this->path = $path;
        $this->identifier = $identifier;
    }

    /**
     * Returns the baseline error message, without regex delimiters.
     * Note: the message may still contain escaped regex meta characters.
     */
    public function unwrapMessage(): string {
        if ($this->rawMessage !== null) {
            return $this->rawMessage;
        }

        if ($this->message === null) {
            return '';
        }

        $msg = $this->message;
        $msg = str_replace(['\\-', '\\.', '%%'], ['-', '.', '%'], $msg);
        $msg = trim($msg, '#^$');
        return $msg;
    }

    public function isDeprecationError(): bool
    {
        $message = $this->unwrapMessage();

        return str_contains($message, ' deprecated class ')
            || str_contains($message, ' deprecated method ')
            || str_contains($message, ' deprecated function ')
            || str_contains($message, ' deprecated property ');
    }

    public function isComplexityError(): bool
    {
        return sscanf($this->unwrapMessage(), BaselineAnalyzer::CLASS_COMPLEXITY_ERROR_MESSAGE, $value, $limit) > 0;
    }

    public function isInvalidPhpDocError(): bool
    {
        return str_contains($this->unwrapMessage(), 'PHPDoc tag ');
    }

    public function isUnknownTypeError(): bool
    {
        $message = $this->unwrapMessage();

        return preg_match('/Instantiated class .+ not found/', $message, $matches) === 1
            || str_contains($message, 'on an unknown class')
            || str_contains($message, 'has invalid type unknown')
            || str_contains($message, 'unknown_type as its type');
    }

    public function isAnonymousVariableError(): bool
    {
        return str_contains($this->unwrapMessage(), 'Anonymous variable');
    }

    public function isUnusedSymbolError(): bool
    {
        return str_ends_with($this->unwrapMessage(), 'is never used');
    }
}
