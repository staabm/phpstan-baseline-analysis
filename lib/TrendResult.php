<?php

namespace staabm\PHPStanBaselineAnalysis;

final class TrendResult
{
    public string $headline;

    /**
     * @var array<ResultPrinter::KEY_*, array{reference: int, comparing: int, trend: string}>
     */
    public array $results;

    public function __construct(string $headline)
    {
        $this->headline = $headline;
        $this->results = [];
    }

    /**
     * @param ResultPrinter::KEY_* $key
     * @param int $referenceValue
     * @param int $comparingValue
     * @return void
     */
    public function setKey(string $key, $referenceValue, $comparingValue, string $trend): void
    {
        $this->results[$key] = [
            'reference' => $referenceValue,
            'comparing' => $comparingValue,
            'trend' => $trend,
        ];
    }
}
