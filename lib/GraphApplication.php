<?php

namespace staabm\PHPStanBaselineAnalysis;

use \Iterator;
use function Safe\file_get_contents;
use function Safe\json_decode;
use function Safe\glob;

final class GraphApplication
{
    /**
     * @throws \Safe\Exceptions\JsonException
     *
     * @throws \Safe\Exceptions\FilesystemException
     */
    public function start(string $jsonGlob): int
    {
        $jsonFiles = glob($jsonGlob);

        $it = $this->iterateOverFiles($jsonFiles);

        $graph = new GraphTemplate();
        // XXX echo
        file_put_contents('./test.html', $graph->render($it));

        return 0;
    }

    public function help(): void
    {
        printf("USAGE: phpstan-baseline-graph '<glob-pattern>'");
    }

    /**
     * @param list<string> $jsonFiles
     * @return Iterator<array{string, AnalyzerResult}>
     */
    private function iterateOverFiles(array $jsonFiles): Iterator
    {
        $reader = new AnalyzerResultReader();
        foreach ($jsonFiles as $jsonFile) {
            $results = $reader->readFile($jsonFile);

            foreach ($results as $baselinePath => $analyzerResult) {
                yield [$baselinePath, $analyzerResult];
            }
        }
    }
}