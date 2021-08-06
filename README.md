Analyzes phpstan baseline files
-------------------------------

## Supported PHPStan Rules
- Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule

## example report

```
$ phpstan-baseline-analyze app/*phpstan-baseline.neon
Analyzing app/portal/phpstan-baseline.neon
  Classes-Cognitive-Complexity: 270
```

## example trend analysis

the following example shows the evolution of errors in your phpstan baselines.
see the trend between 2 different points in time like:

```
$ git clone ...

$ phpstan-baseline-analyze app/*phpstan-baseline.neon --json > reference.json

$ git checkout `git rev-list -n 1 --before="1 week ago" HEAD`

$ phpstan-baseline-analyze app/*phpstan-baseline.neon --json > now.json

$ phpstan-baseline-trend reference.json now.json
Analyzing Trend for app/portal/phpstan-baseline.neon
  Classes-Cognitive-Complexity: 309 -> 177 => improved
```
