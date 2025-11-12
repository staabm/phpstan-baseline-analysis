<?php

namespace staabm\PHPStanBaselineAnalysis;

use Nette\DI\Helpers;
use Nette\Neon\Neon;
use PHPStan\ShouldNotHappenException;

final class FilterApplication
{
    /**
     * @api
     */
    const EXIT_SUCCESS = 0;
    /**
     * @api
     */
    const EXIT_ERROR = 1;

    /**
     * @return self::EXIT_*
     *
     * @api
     */
    public function start(string $glob, FilterConfig $filterConfig): int
    {
        $baselines = BaselineFinder::forGlob($glob);
        $numBaselines = count($baselines);

        for($i = 0; $i < $numBaselines; $i++) {
            $baseline = $baselines[$i];

            $filter = new BaselineFilter($baseline);
            $errors = $filter->filter($filterConfig);

            $this->printResult($errors);
        }

        if ($numBaselines > 0) {
            return self::EXIT_SUCCESS;
        }

        return self::EXIT_ERROR;
    }

    /**
     * @api
     */
    public function help(): void
    {
        echo 'USAGE: phpstan-baseline-filter <GLOB-PATTERN> [--exclude=<FILTER-KEY>,...] [--include=<FILTER-KEY>,...]'.PHP_EOL.PHP_EOL;
        printf('valid FILTER-KEYs: %s', implode(', ', ResultPrinter::getFilterKeys()));

        echo PHP_EOL;
    }

    /**
     * @param list<BaselineError> $errors
     */
    private function printResult(array $errors): void
    {
        $ignoreErrors = [];
        foreach ($errors as $error) {
            $ignoreError = [];

            if ($error->message !== null) {
                $ignoreError['message'] = $error->message;
            }

            $ignoreError['count'] = $error->count;

            if ($error->path !== null) {
                $ignoreError['path'] = $error->path;
            }
            if ($error->identifier !== null) {
                $ignoreError['identifier'] = $error->identifier;
            }
            if ($error->rawMessage !== null) {
                $ignoreError['rawMessage'] = $error->rawMessage;
            }

            $ignoreErrors[] = $ignoreError;

        }

        // encode analog PHPStan
        $neon = Neon::encode([
            'parameters' => [
                'ignoreErrors' => $ignoreErrors,
            ],
        ], true);

        if (substr($neon, -2) !== "\n\n") {
            throw new ShouldNotHappenException();
        }

        echo substr($neon, 0, -1);
    }
}
