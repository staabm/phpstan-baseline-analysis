<?php

namespace staabm\PHPStanBaselineAnalysis;

use Nette\Neon\Neon;

final class BaselineFinder
{
    /**
     * @return Baseline[]
     */
    static public function forGlob(string $glob): array
    {
        $baselines = [];

        foreach (self::rglob($glob) as $baseline) {
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

    // from https://stackoverflow.com/a/17161106
    static private function rglob($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, self::rglob($dir . '/' . basename($pattern), $flags));
        }
        return $files;
    }
}