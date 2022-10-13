<?php

namespace staabm\PHPStanBaselineAnalysis;

use Iterator;
use function Safe\json_encode;

final class GraphTemplate
{
    const COMPLEXITY_COLOR = '#C24642';

    /**
     * @param Iterator<array{string, AnalyzerResult}> $it
     */
    public function render(Iterator $it):string {
        $splines = [];
        $dates = [];
        $dataByDates = [];
        foreach($it as $data) {
            /** @var AnalyzerResult $analyzerResult */
            list($baselinePath, $analyzerResult) = $data;

            if ($analyzerResult->referenceDate === null) {
                continue;
            }

            if (!array_key_exists($baselinePath, $splines)) {
                $splines[$baselinePath] = [];

                $splines[$baselinePath][0] = ['label' => ResultPrinter::KEY_OVERALL_ERRORS, 'borderColor' => 'blue', 'data' => []];
                $splines[$baselinePath][1] = ['label' => ResultPrinter::KEY_CLASSES_COMPLEXITY, 'borderColor' => self::COMPLEXITY_COLOR, 'data' => []];
                $splines[$baselinePath][2] = ['label' => ResultPrinter::KEY_DEPRECATIONS, 'borderColor' => 'lightgreen', 'data' => []];
                $splines[$baselinePath][3] = ['label' => ResultPrinter::KEY_INVALID_PHPDOCS, 'borderColor' => 'lightblue', 'data' => []];
                $splines[$baselinePath][4] = ['label' => ResultPrinter::KEY_UNKNOWN_TYPES, 'borderColor' => 'purple', 'data' => []];
                $splines[$baselinePath][5] = ['label' => ResultPrinter::KEY_ANONYMOUS_VARIABLES, 'borderColor' => 'pink', 'data' => []];
            }

            $dataByDates[$baselinePath][$analyzerResult->referenceDate->getTimestamp()] = [
                $analyzerResult->overallErrors,
                $analyzerResult->classesComplexity,
                $analyzerResult->deprecations,
                $analyzerResult->invalidPhpdocs,
                $analyzerResult->unknownTypes,
                $analyzerResult->anonymousVariables,
            ];
        }

        foreach ($dataByDates as $baselinePath => $dataByDate) {
            foreach ($dataByDate as $date => $data) {
                $dates[$baselinePath][] = 'new Date(' . $date . ' * 1000).toLocaleDateString("de-DE")';
                $splines[$baselinePath][0]['data'][] = $data[0];
                $splines[$baselinePath][1]['data'][] = $data[1];
                $splines[$baselinePath][2]['data'][] = $data[2];
                $splines[$baselinePath][3]['data'][] = $data[3];
                $splines[$baselinePath][4]['data'][] = $data[4];
                $splines[$baselinePath][5]['data'][] = $data[5];
            }
        }

        $chartsHtml = '';
        foreach($splines as $baselinePath => $data) {
            $chartData = [
                'labels' => $dates[$baselinePath],
                'datasets' => $data
            ];
            $chartsHtml .= '
                    <canvas id="chartContainer'. md5($baselinePath).'" style="height: 370px; width: 100%; margin-bottom: 30px;"></canvas>
                    <script>
                    (function () {
                        const ctx = document.getElementById(\'chartContainer' . md5($baselinePath) . '\');
                        const chart = new Chart(ctx, {
                            type: \'line\',
                            options: {
                                plugins: {
                                    title: {
                                        display: true,
                                        text: \'PHPStan Baseline Analysis '. $baselinePath .'\',
                                    }
                                }
                            },
                            data: {
                                labels:  [' . implode(', ', $chartData['labels']) . '],
                                datasets: ' . json_encode($chartData['datasets']) . '
                            }
                        });
                    })();
                </script>
';
        }

        return '
            <!DOCTYPE HTML>
            <html>
            <head></head>
            <body>
                <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
                '. $chartsHtml.'
            </body>
            </html>
        ';
    }
}