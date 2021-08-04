<?php

namespace staabm\PHPStanBaselineAnalysis;

use Nette\Neon\Neon;

final class Baseline {
    /**
     * @var array
     */
    private $content;

    /**
     * @var string
     */
    private $filePath;

    static public function forFile(string $filePath):self {

        $content = file_get_contents($filePath);
        $decoded = Neon::decode($content);

        if (!is_array($decoded)) {
            throw new \RuntimeException(sprintf('expecting baseline %s to be non-empty', $filePath));
        }

        $baseline = new self();
        $baseline->content = $decoded;
        $baseline->filePath = $filePath;
        return $baseline;
    }

    /**
     * @return Iterator<string>
     */
    public function getIgnoreErrors() {
        if (!array_key_exists('parameters', $this->content)) {
            throw new \RuntimeException('missing paramters from baseline');
        }
        $parameters = $this->content['parameters'];

        if (!array_key_exists('ignoreErrors', $parameters)) {
            throw new \RuntimeException('missing ignoreErrors from baseline');
        }
        $ignoreErrors = $parameters['ignoreErrors'];

        /**
         * @var array{message: string, count: int, path: string} $error
         */
        foreach($ignoreErrors as $error) {
            yield $error['message'];
        }
    }

    public function getFilePath():string {
        return $this->filePath;
    }
}