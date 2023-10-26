<?php

namespace staabm\PHPStanBaselineAnalysis;

use Iterator;
use Nette\Neon\Neon;
use RuntimeException;
use Safe\Exceptions\FilesystemException;
use function Safe\file_get_contents;
use function Safe\sprintf;

final class Baseline {
    /**
     * @var array{parameters?: array{ignoreErrors?: list<array{message: string, count: int, path: string}>}}
     */
    private $content;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @throws FilesystemException
     */
    static public function forFile(string $filePath):self {
        $baselineExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        if ($baselineExtension === 'php') {
            $decoded = require $filePath;
        } else {
            $content = file_get_contents($filePath);
            $decoded = Neon::decode($content);
        }

        if (!is_array($decoded)) {
            throw new RuntimeException(sprintf('expecting baseline %s to be non-empty', $filePath));
        }

        $baseline = new self();
        $baseline->content = $decoded;
        $baseline->filePath = $filePath;
        return $baseline;
    }

    /**
     * @return Iterator<BaselineError>
     */
    public function getIgnoreErrors(): Iterator {
        if (!array_key_exists('parameters', $this->content) || !is_array($this->content['parameters'])) {
            throw new RuntimeException(sprintf('missing parameters from baseline %s', $this->filePath));
        }
        $parameters = $this->content['parameters'];

        if (!array_key_exists('ignoreErrors', $parameters) || !is_array($parameters['ignoreErrors'])) {
            throw new RuntimeException(sprintf('missing ignoreErrors from baseline %s', $this->filePath));
        }
        $ignoreErrors = $parameters['ignoreErrors'];

        foreach($ignoreErrors as $error) {
            yield new BaselineError($error['count'], $error['message'], $error['path']);
        }
    }

    public function getFilePath():string {
        return $this->filePath;
    }
}
