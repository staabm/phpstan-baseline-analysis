<?php

namespace staabm\PHPStanBaselineAnalysis;

final class ComparisonResult
{
    public string $headline;

    /**
     * @var array<string, array{reference: int, comparing: int, trend: string}>
     */
    public array $output;

    public function __construct(string $headline)
    {
        $this->headline = $headline;
        $this->output = [];
    }
}
