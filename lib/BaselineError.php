<?php

namespace staabm\PHPStanBaselineAnalysis;

final class BaselineError
{
    /**
     * @var int
     */
    public $count;

    /**
     * @var string
     */
    public $message;

    /**
     * Returns the baseline error message, without regex delimiters.
     * Note: the message may still contain escaped regex meta characters.
     * 
     * @return string
     */
    public function unwrapMessage(): string {
        return trim($this->message, '#^$');
    }
}
