<?php declare(strict_types=1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AccountController\\:\\:\\$anredekurz\\.$#',
    'count' => 2,
    'path' => '/controllers/AccountController.php',
];
$ignoreErrors[] = [
    'message' => '#^Class cognitive complexity is 48, keep it under 30$#',
    'count' => 1,
    'path' => '/controllers/ApplicationController.php',
];
$ignoreErrors[] = [
    'message' => '#^Cognitive complexity for "ApplicationController\\:\\:app_init\\(\\)" is 12, keep it under 10$#',
    'count' => 1,
    'path' => '/controllers/ApplicationController.php',
];
$ignoreErrors[] = [
    'message' => '#^Cognitive complexity for "ApplicationController\\:\\:check_breadrumbs\\(\\)" is 16, keep it under 10$#',
    'count' => 1,
    'path' => '/controllers/ApplicationController.php',
];
$ignoreErrors[] = [
    'message' => '#^Class cognitive complexity is 22, keep it under 30$#',
    'count' => 1,
    'path' => '/controllers/ApplicationController.php',
];
$ignoreErrors[] = [
    'message' => '#^Construct empty\\(\\) is not allowed\\. Use more strict comparison\\.$#',
    'count' => 10,
    'path' => '/controllers/ApplicationController.php',
];
$ignoreErrors[] = [
    'message' => '#^Cognitive complexity for "TablePdf\\:\\:NbLines\\(\\)" is 17, keep it under 10$#',
    'count' => 1,
    'path' => '/models/TablePdf.php',
];
$ignoreErrors[] = [
    'message' => '#^Use explicit return value over magic &reference$#',
    'count' => 1,
    'path' => '/models/TablePdf.php',
];
$ignoreErrors[] = [
    'message' => '#^Construct empty\\(\\) is not allowed\\. Use more strict comparison\\.$#',
    'count' => 1,
    'path' => '/models/Tarif.php',
];
$ignoreErrors[] = [
    'message' => '#^Use explicit names over dynamic ones$#',
    'count' => 1,
    'path' => '/models/Zubehoer.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Zugabe\\:\\:\\$feat\\.$#',
    'count' => 1,
    'path' => '/models/Zugabe.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Zugabe\\:\\:\\$hersteller\\.$#',
    'count' => 1,
    'path' => '/models/Zugabe.php',
];
$ignoreErrors[] = [
    'message' => '#^Use explicit names over dynamic ones$#',
    'count' => 1,
    'path' => '/models/Zugabe.php',
];
$ignoreErrors[] = [
    'message' => '#^Instantiation of deprecated class Zend_Db_Expr\\.$#',
    'count' => 2,
    'path' => '/controllers/AccountWatchlistController.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @throws with type Throws is not subtype of Throwable$#',
    'count' => 5,
    'path' => '/lib/rest/RestClient.php',
];
$ignoreErrors[] = [
    'message' => '#^Property ActionMailer\\:\\:\\$template_root has unknown class unknown_type as its type\\.$#',
    'count' => 1,
    'path' => '/lib/ActionMailer.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous variable in a `\\$lang\\-\\>\\.\\.\\.\\(\\)` method call can lead to false dead methods\\. Make sure the variable type is known$#',
    'count' => 1,
    'path' => '/application/Rocket/lib/Prospect/Service/ProspectDetailService.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous variables in a "\\$element\\-\\>value\\[0\\]\\-\\>\\.\\.\\." property fetch can lead to false dead property\\. Make sure the variable type is known$#',
    'count' => 3,
    'path' => '/application/Rocket/lib/bulkConsumerCheckout/integration/VatValidationClient.php',
];
$ignoreErrors[] = [
    'message' => '#^Out of 1 possible property types, only 1 %% actually have it\\. Add more property types to get over 99 %%$#',
    'count' => 1,
    'path' => '/N/A',
];
$ignoreErrors[] = [
    'message' => '#^Out of 22 possible return types, only 4 %% actually have it\\. Add more return types to get over 99 %%$#',
    'count' => 1,
    'path' => '/N/A',
];
$ignoreErrors[] = [
    'message' => '#^Out of 33 possible param types, only 27 %% actually have it\\. Add more param types to get over 99 %%$#',
    'count' => 1,
    'path' => '/N/A',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
