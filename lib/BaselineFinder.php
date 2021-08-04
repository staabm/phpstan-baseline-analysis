<?php

namespace staabm\PHPStanBaselineAnalysis;

use Nette\Neon\Neon;

final class BaselineFinder
{
    /**
     * @return Baseline[]
     */
    static public function forGlob(string $glob):array
    {
        $baselines = [];

        foreach (glob($glob) as $baseline) {
            if (!is_file($baseline)) {
                continue;
            }

            if (!str_ends_with($baseline, '.neon')) {
                continue;
            }

            $baselines[] = Baseline::forFile($baseline);
        }

        return $baselines;
    }
}