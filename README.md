Analyzes phpstan baseline files
-------------------------------

Analyzes PHPStan baseline files and creates aggregated error trend-reports.

## Supported PHPStan RuleSets
- https://github.com/phpstan/phpstan-deprecation-rules

## Supported PHPStan Rules
- PHPStan\Rules\PhpDoc\InvalidPhpDocTagValueRule
- Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule
- Symplify\PHPStanRules\Rules\Explicit\NoMixedMethodCallerRule
- Symplify\PHPStanRules\Rules\Explicit\NoMixedPropertyFetcherRule

## example report

```
$ phpstan-baseline-analyze *phpstan-baseline.neon
Analyzing app/portal/phpstan-baseline.neon
  Classes-Cognitive-Complexity: 270
  Deprecations: 2
  Invalid-Phpdocs: 5
  Unknown-Types: 1
  Anonymous-Variables: 4
```

## example trend analysis

the following example shows the evolution of errors in your phpstan baselines.
see the trend between 2 different points in time like:

```
$ git clone ...

$ phpstan-baseline-analyze *phpstan-baseline.neon --json > now.json

$ git checkout `git rev-list -n 1 --before="1 week ago" HEAD`

$ phpstan-baseline-analyze *phpstan-baseline.neon --json > reference.json

$ phpstan-baseline-trend reference.json now.json
Analyzing Trend for app/portal/phpstan-baseline.neon
  Classes-Cognitive-Complexity: 309 -> 177 => improved
  Deprecations: 1 -> 2 => worse
  Invalid-Phpdocs: 3 -> 1 => good
  Unknown-Types: 5 -> 15 => worse
  Anonymous-Variables: 4 -> 3 => good
```
