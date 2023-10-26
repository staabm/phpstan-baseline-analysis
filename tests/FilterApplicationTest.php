<?php

namespace staabm\PHPStanBaselineAnalysis\Tests;

use staabm\PHPStanBaselineAnalysis\FilterApplication;
use staabm\PHPStanBaselineAnalysis\ResultPrinter;

final class FilterApplicationTest extends BaseTestCase
{
    function testTextPrinting():void
    {
        $app = new FilterApplication();

        ob_start();
        $exitCode = $app->start(__DIR__ . '/fixtures/all-in.neon', ResultPrinter::KEY_DEPRECATIONS);
        $rendered = ob_get_clean();

        $rendered = str_replace(__DIR__, '', $rendered);

        $expected = <<<PHP
parameters:
	ignoreErrors:
		-
			message: "#^Instantiation of deprecated class Zend_Db_Expr\\.$#"
			count: 2
			path: controllers/AccountWatchlistController.php

PHP;

        $this->assertSame($expected, $rendered);
        $this->assertSame(0, $exitCode);
    }

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
