<?php

namespace staabm\PHPStanBaselineAnalysis;

use Safe\Exceptions\FilesystemException;
use function Safe\glob;

final class BaselineFinder
{
    /**
     * @return Baseline[]
     *
     * @throws FilesystemException
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

    /**
     * from https://stackoverflow.com/a/17161106
     *
     * @return string[]
     *
     * @throws FilesystemException
     */
    static private function rglob(string $pattern,int $flags = 0):array
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            if (basename($dir) == 'vendor') {
                continue;
            }

            $files = array_merge($files, self::rglob($dir . '/' . basename($pattern), $flags));
        }
        return $files;
    }
}