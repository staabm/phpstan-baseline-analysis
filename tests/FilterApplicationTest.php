<?php

namespace staabm\PHPStanBaselineAnalysis\Tests;

use staabm\PHPStanBaselineAnalysis\FilterApplication;
use staabm\PHPStanBaselineAnalysis\ResultPrinter;

final class FilterApplicationTest extends BaseTestCase
{
    function testNoMatchingGlob():void
    {
        $app = new FilterApplication();

        ob_start();
        $exitCode = $app->start('this/file/does/not/exist*baseline.neon', ResultPrinter::KEY_UNUSED_SYMBOLS);
        $rendered = ob_get_clean();

        $this->assertSame('', $rendered);
        $this->assertSame(1, $exitCode);
    }
}
