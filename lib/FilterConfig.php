<?php

namespace staabm\PHPStanBaselineAnalysis;

use function Safe\substr;

/**
 * @immutable
 */
final class FilterConfig {
    /**
     * @var array<ResultPrinter::KEY_*>
     */
    private array $excluded;
    /**
     * @var array<ResultPrinter::KEY_*>
     */
    private array $included;

    /**
     * @param array<ResultPrinter::KEY_*> $excluded
     * @param array<ResultPrinter::KEY_*> $included
     */
    private function __construct(array $excluded, array $included) {
        $this->excluded = $excluded;
        $this->included = $included;
    }

    static public function fromArgs(string $args): self {
        $args = explode(' ', $args);

        $excluded = [];
        $included = [];
        foreach ($args as $arg) {
            if (str_starts_with($arg, '--exclude=')) {
                foreach(explode(',', substr($arg, 10)) as $key) {
                    if (!ResultPrinter::isFilterKey($key)) {
                        throw new \Exception("Invalid filter key: $key");
                    }
                    $excluded[] = $key;
                }
            } else if (str_starts_with($arg, '--include=')) {
                foreach(explode(',', substr($arg, 10)) as $key) {
                    if (!ResultPrinter::isFilterKey($key)) {
                        throw new \Exception("Invalid filter key: $key");
                    }
                    $included[] = $key;
                }
            }
        }

        if (count($excluded) > 0 && count($included) > 0) {
            throw new \Exception("Cannot use --exclude and --include at the same time");
        }

        return new self($excluded, $included);
    }

    public function isExcluding(): bool {
        return count($this->excluded) > 0;
    }

    /**
     * @param ResultPrinter::KEY_* $key
     */
    public function containsKey(string $key): bool {
        return in_array($key, $this->excluded, true) || in_array($key, $this->included, true);
    }
}