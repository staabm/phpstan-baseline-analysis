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
    public array $excluded = [];
    /**
     * @var array<ResultPrinter::KEY_*>
     */
    public array $included = [];

    static public function fromArgs(string $args): self {
        $config = new FilterConfig();
        $args = explode(' ', $args);
        foreach ($args as $arg) {
            if (str_starts_with($arg, '--exclude=')) {
                foreach(explode(',', substr($arg, 10)) as $key) {
                    if (!ResultPrinter::isFilterKey($key)) {
                        throw new \Exception("Invalid filter key: $key");
                    }
                    $config->excluded[] = $key;
                }
            } else if (str_starts_with($arg, '--include=')) {
                foreach(explode(',', substr($arg, 10)) as $key) {
                    if (!ResultPrinter::isFilterKey($key)) {
                        throw new \Exception("Invalid filter key: $key");
                    }
                    $config->included[] = $key;
                }
            }
        }

        if (count($config->excluded) > 0 && count($config->included) > 0) {
            throw new \Exception("Cannot use --exclude and --include at the same time");
        }

        return $config;
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