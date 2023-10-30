Analyzes phpstan baseline files
-------------------------------

Analyzes PHPStan baseline files and creates aggregated error trend-reports.

[Read more in the Blog post.](https://staabm.github.io/2022/07/04/phpstan-baseline-analysis.html)

You need at least one of the supported PHPStan RuleSets/Rules configured in your project, to get meaningful results.

## Installation

```
composer require staabm/phpstan-baseline-analysis --dev
```

## Supported PHPStan RuleSets
- https://github.com/phpstan/phpstan-deprecation-rules

## Supported PHPStan Rules
- PHPStan\Rules\PhpDoc\InvalidPhpDocTagValueRule

## Supported [Symplify PHPStan Rules](https://github.com/symplify/phpstan-rules)
- Symplify\PHPStanRules\Rules\Explicit\NoMixedMethodCallerRule
- Symplify\PHPStanRules\Rules\Explicit\NoMixedPropertyFetcherRule

## Supported [tomasvotruba/cognitive-complexity](https://github.com/TomasVotruba/cognitive-complexity) Rules
- TomasVotruba\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule

## Supported [tomasvotruba/type-coverage](https://github.com/TomasVotruba/type-coverage) Rules
- TomasVotruba\TypeCoverage\Rules\ParamTypeCoverageRule
- TomasVotruba\TypeCoverage\Rules\PropertyTypeCoverageRule
- TomasVotruba\TypeCoverage\Rules\ReturnTypeCoverageRule

## Supported [tomasvotruba/unused-public](https://github.com/TomasVotruba/unused-public) Rules
- TomasVotruba\UnusedPublic\Rules\UnusedPublicClassConstRule
- TomasVotruba\UnusedPublic\Rules\UnusedPublicClassMethodRule
- TomasVotruba\UnusedPublic\Rules\UnusedPublicPropertyRule


## example report

```
$ phpstan-baseline-analyze *phpstan-baseline.neon
Analyzing app/portal/phpstan-baseline.neon
  Overall-Errors: 41
  Classes-Cognitive-Complexity: 70
  Deprecations: 2
  Invalid-Phpdocs: 5
  Unknown-Types: 1
  Anonymous-Variables: 4
  Native-Property-Type-Coverage: 1
  Native-Param-Type-Coverage: 27
  Native-Return-Type-Coverage: 4
  Unused-Symbols: 3
```

## example error filtering

Filter a existing baseline and output only errors NOT matching the given filter key:
_This can be helpful to remove a class of errors out of an existing baseline, so PHPStan will start reporting them again._
```
$ phpstan-baseline-filter *phpstan-baseline.neon --exclude=Unknown-Types
```

Filter a existing baseline and output only errors matching the given filter key:
```
$ phpstan-baseline-filter *phpstan-baseline.neon --include=Invalid-Phpdocs
```

## example graph analysis

```
$ git clone ...

$ phpstan-baseline-analyze *phpstan-baseline.neon --json > now.json

$ git checkout `git rev-list -n 1 --before="1 week ago" HEAD`
$ phpstan-baseline-analyze '*phpstan-baseline.neon' --json > 1-week-ago.json

$ git checkout `git rev-list -n 1 --before="2 week ago" HEAD`
$ phpstan-baseline-analyze '*phpstan-baseline.neon' --json > 2-weeks-ago.json

$ php phpstan-baseline-graph '*.json' > result.html
```

![PHPStan baseline analysis graph](https://github.com/staabm/phpstan-baseline-analysis/assets/120441/ea5abe25-21e8-43f2-9118-0967a75517c6)


## example trend analysis

the following example shows the evolution of errors in your phpstan baselines.
see the trend between 2 different points in time like:

```
$ git clone ...

$ phpstan-baseline-analyze '*phpstan-baseline.neon' --json > now.json

$ git checkout `git rev-list -n 1 --before="1 week ago" HEAD`

$ phpstan-baseline-analyze '*phpstan-baseline.neon' --json > reference.json

$ phpstan-baseline-trend reference.json now.json
Analyzing Trend for app/portal/phpstan-baseline.neon
  Overall-Errors: 30 -> 17 => improved
  Classes-Cognitive-Complexity: 309 -> 177 => improved
  Deprecations: 1 -> 2 => worse
  Invalid-Phpdocs: 3 -> 1 => good
  Unknown-Types: 5 -> 15 => worse
  Anonymous-Variables: 4 -> 3 => good
  Unused-Symbols: 1 -> 1 => good
  Native-Return-Type-Coverage: 20 -> 2 => worse
  Native-Property-Type-Coverage: 3 -> 3 => good
  Native-Param-Type-Coverage: 4 -> 40 => improved
```

## Usage example in a scheduled GitHub Action with Mattermost notification

Copy the following workflow into your repository. Make sure to adjust as needed:
- adjust the cron schedule pattern
- actions/checkout might require a token - e.g. for private repos
- adjust the comparison period, as you see fit
- adjust the notification to your needs - e.g. use Slack, Discord, E-Mail,..

```
name: Trends Analyse

on:
  workflow_dispatch:
  schedule:
    - cron: '0 8 * * 4'

jobs:

  behat:
    name: Trends
    runs-on: ubuntu-latest
    timeout-minutes: 10

    steps:
      - run: "composer global require staabm/phpstan-baseline-analysis"
      - run: echo "$(composer global config bin-dir --absolute --quiet)" >> $GITHUB_PATH

      - uses: actions/checkout@v2
        with:
          fetch-depth: 50 # fetch the last X commits.

      - run: "phpstan-baseline-analyze '*phpstan-baseline.neon' --json > ../now.json"

      - run: git checkout `git rev-list -n 1 --before="1 week ago" HEAD`

      - run: "phpstan-baseline-analyze '*phpstan-baseline.neon' --json > ../reference.json"

      - name: analyze trend
        shell: php {0}
        run: |
          <?php
          exec('phpstan-baseline-trend ../reference.json ../now.json > ../trend.txt', $output, $exitCode);
          $project = '${{ github.repository }}';

          if ($exitCode == 0) {
            # improvements
            file_put_contents(
              'mattermost.json',
              json_encode(["username" => "github-action-trend-bot", "text" => $project ." :tada:\n". file_get_contents("../trend.txt")])
            );
          }
          elseif ($exitCode == 1) {
            # steady
            file_put_contents(
              'mattermost.json',
              json_encode(["username" => "github-action-trend-bot", "text" => $project ." :green_heart:\n". file_get_contents("../trend.txt")])
            );
          }
          elseif ($exitCode == 2) {
            # got worse
            file_put_contents(
              'mattermost.json',
              json_encode(["username" => "github-action-trend-bot", "text" => $project ." :broken_heart:\n". file_get_contents("../trend.txt")])
            );
          }

      - run: 'curl -X POST -H "Content-Type: application/json" -d @mattermost.json ${{ secrets.MATTERMOST_WEBHOOK_URL }}'
        if: always()

```

## 💌 Give back some love

[Consider supporting the project](https://github.com/sponsors/staabm), so we can make this tool even better even faster for everyone.
