<?php

namespace staabm\PHPStanBaselineAnalysis;

use Iterator;
final class GraphTemplate
{
    private const COMPLEXITY_COLOR = '#C24642';

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

                $splines[$baselinePath][0] = [
                    'label' => ResultPrinter::KEY_OVERALL_ERRORS,
                    'borderColor' => 'blue',
                    'data' => []
                ];
                $splines[$baselinePath][1] = [
                    'label' => ResultPrinter::KEY_CLASSES_COMPLEXITY,
                    'borderColor' => self::COMPLEXITY_COLOR,
                    'data' => []
                ];
                $splines[$baselinePath][2] = [
                    'label' => ResultPrinter::KEY_DEPRECATIONS,
                    'borderColor' => 'lightgreen',
                    'data' => []
                ];
                $splines[$baselinePath][3] = [
                    'label' => ResultPrinter::KEY_INVALID_PHPDOCS,
                    'borderColor' => 'lightblue',
                    'data' => []
                ];
                $splines[$baselinePath][4] = [
                    'label' => ResultPrinter::KEY_UNKNOWN_TYPES,
                    'borderColor' => 'purple',
                    'data' => []
                ];
                $splines[$baselinePath][5] = [
                    'label' => ResultPrinter::KEY_ANONYMOUS_VARIABLES,
                    'borderColor' => 'pink',
                    'data' => []
                ];
                $splines[$baselinePath][6] = [
                    'label' => ResultPrinter::KEY_PROPERTY_TYPE_COVERAGE,
                    'yAxisID' => 'yPercent',
                    'borderColor' => 'lightcoral',
                    'borderWidth' => 2,
                    'type' => 'bar',
                    'data' => []
                ];
                $splines[$baselinePath][7] = [
                    'label' => ResultPrinter::KEY_PARAM_TYPE_COVERAGE,
                    'yAxisID' => 'yPercent',
                    'borderColor' => 'lightseagreen',
                    'borderWidth' => 2,
                    'type' => 'bar',
                    'data' => []
                ];
                $splines[$baselinePath][8] = [
                    'label' => ResultPrinter::KEY_RETURN_TYPE_COVERAGE,
                    'yAxisID' => 'yPercent',
                    'borderColor' => 'lightsteelblue',
                    'borderWidth' => 2,
                    'type' => 'bar',
                    'data' => []
                ];
                $splines[$baselinePath][9] = [
                    'label' => ResultPrinter::KEY_UNUSED_SYMBOLS,
                    'borderColor' => 'lightyellow',
                    'data' => []
                ];
            }

            $dataByDates[$baselinePath][$analyzerResult->referenceDate->getTimestamp()] = [
                $analyzerResult->overallErrors,
                $analyzerResult->classesComplexity,
                $analyzerResult->deprecations,
                $analyzerResult->invalidPhpdocs,
                $analyzerResult->unknownTypes,
                $analyzerResult->anonymousVariables,
                $analyzerResult->propertyTypeCoverage,
                $analyzerResult->paramTypeCoverage,
                $analyzerResult->returnTypeCoverage,
                $analyzerResult->unusedSymbols,
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
                $splines[$baselinePath][6]['data'][] = $data[6];
                $splines[$baselinePath][7]['data'][] = $data[7];
                $splines[$baselinePath][8]['data'][] = $data[8];
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
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                    },
                                    y: {
                                        beginAtZero: true,
                                    },
                                    yPercent: {
                                        min: 0,
                                        max: 100,
                                        beginAtZero: true,
                                        position: \'right\',
                                        grid: {
                                            display: false,
                                        },
                                    },
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