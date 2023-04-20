<?php

namespace staabm\PHPStanBaselineAnalysis\Tests;

use staabm\PHPStanBaselineAnalysis\AnalyzeApplication;
use staabm\PHPStanBaselineAnalysis\GraphApplication;
use staabm\PHPStanBaselineAnalysis\ResultPrinter;
use function Safe\json_encode;
use function Safe\file_get_contents;

class GraphApplicationTest extends BaseTestCase
{
    function testTextPrinting():void
    {
        $app = new GraphApplication();

        ob_start();
        $exitCode = $app->start(__DIR__ . '/fixtures/graph*.json');
        $rendered = ob_get_clean();

        // file_put_contents(__DIR__ . '/fixtures/graph.html.expected', $rendered);
        // file_put_contents(__DIR__ . '/fixtures/graph.html', $rendered);
        $this->assertSame(file_get_contents(__DIR__ . '/fixtures/graph.html.expected'), $rendered);
        $this->assertSame(0, $exitCode);
    }

}
