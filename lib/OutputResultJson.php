<?php

namespace staabm\PHPStanBaselineAnalysis;

final class OutputResultJson
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
