<?php

namespace staabm\PHPStanBaselineAnalysis;

use \Iterator;
use function Safe\glob;

final class GraphApplication
{
    /**
     * @throws \Safe\Exceptions\JsonException
     *
     * @throws \Safe\Exceptions\FilesystemException
     *
     * @api
     */
    public function start(string $jsonGlob): int
    {
        $jsonFiles = glob($jsonGlob);

        $it = $this->iterateOverFiles($jsonFiles);

        $graph = new GraphTemplate();
        echo $graph->render($it);

        return 0;
    }

    /**
     * @api
     */
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
            if (strpos($jsonFile, '.json') === false) {
                throw new \RuntimeException('Expecting json file, got ' . $jsonFile);
            }

            $results = $reader->readFile($jsonFile);

            foreach ($results as $baselinePath => $analyzerResult) {
                yield [$baselinePath, $analyzerResult];
            }
        }
    }
}