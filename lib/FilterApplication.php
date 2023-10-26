<?php

namespace staabm\PHPStanBaselineAnalysis;

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
     * @throws \Safe\Exceptions\FilesystemException
     *
     * @api
     */
    public function start(string $glob, string $filterKey): int
    {
        $baselines = BaselineFinder::forGlob($glob);
        $numBaselines = count($baselines);

        for($i = 0; $i < $numBaselines; $i++) {
            $baseline = $baselines[$i];

            $analyzer = new BaselineAnalyzer($baseline);
            $errors = $analyzer->filter($filterKey);

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
        printf('USAGE: phpstan-baseline-filter <GLOB-PATTERN>');
    }

    /**
     * @param list<BaselineError> $errors
     */
    private function printResult(array $errors): void
    {
        printf('parameters:');
        printf("\n\t" . 'ignoreErrors:');

        foreach ($errors as $error) {
            printf("\n\t\t" . '-');
            printf("\n\t\t\t" . $error->getFormattedForOutput());
        }
    }
}
