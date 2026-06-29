<?php

namespace staabm\PHPStanBaselineAnalysis;

final class BaselineFinder
{
    /**
     * @param string[] $excludeFilenames
     * @return Baseline[]
     */
    static public function forGlob(string $glob, array $excludeFilenames = []): array
    {
        $baselines = [];

        foreach (self::rglob($glob, 0, $excludeFilenames) as $baseline) {
            if (!is_file($baseline)) {
                continue;
            }

            if (!str_ends_with($baseline, '.neon') && !str_ends_with($baseline, '.php')) {
                continue;
            }

            $baselines[] = Baseline::forFile($baseline);
        }

        return $baselines;
    }

    /**
     * from https://stackoverflow.com/a/17161106
     *
     * @param string[] $excludeFilenames
     * @return string[]
     */
    static private function rglob(string $pattern, int $flags = 0, array $excludeFilenames = []): array
    {
        $files = glob($pattern, $flags);
        if (!$files) {
            return [];
        }

        // Filter out excluded filenames
        if ($excludeFilenames !== []) {
            $files = self::filerExcludedFiles($files, $excludeFilenames);
        }

        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) ?: [] as $dir) {
            if (basename($dir) == 'vendor') {
                continue;
            }

            $files = array_merge($files, self::rglob($dir . '/' . basename($pattern), $flags, $excludeFilenames));
        }
        return $files;
    }

    /**
     * @param string[] $files
     * @param string[] $excludeFilenames
     * @return string[]
     */
    private static function filerExcludedFiles(array $files, array $excludeFilenames): array
    {
        return array_filter($files, function (string $file) use ($excludeFilenames) {
            foreach ($excludeFilenames as $excludeFilename) {
                if (str_ends_with($file, $excludeFilename)) {
                    return false;
                }
            }

            return true;
        });
    }
}
