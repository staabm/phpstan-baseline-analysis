<?php

namespace staabm\PHPStanBaselineAnalysis;

use Iterator;

final class GraphTemplate
{
    const COMPLEXITY_COLOR = '#C24642';

    /**
     * @param Iterator<array{string, AnalyzerResult}> $it
     */
    public function render(Iterator $it):string {
        $splines = [];
        foreach($it as $data) {
            /** @var AnalyzerResult $analyzerResult */
            list($baselinePath, $analyzerResult) = $data;

            if ($analyzerResult->referenceDate === null) {
                continue;
            }
            $timestamp = $analyzerResult->referenceDate->getTimestamp();

            if (!array_key_exists($baselinePath, $splines)) {
                $splines[$baselinePath] = [];

                $splines[$baselinePath][ResultPrinter::KEY_OVERALL_ERRORS] = [];
                $splines[$baselinePath][ResultPrinter::KEY_CLASSES_COMPLEXITY] = [];
                $splines[$baselinePath][ResultPrinter::KEY_DEPRECATIONS] = [];
                $splines[$baselinePath][ResultPrinter::KEY_INVALID_PHPDOCS] = [];
                $splines[$baselinePath][ResultPrinter::KEY_UNKNOWN_TYPES] = [];
                $splines[$baselinePath][ResultPrinter::KEY_ANONYMOUS_VARIABLES] = [];
            }

            $splines[$baselinePath][ResultPrinter::KEY_OVERALL_ERRORS][] = '{x: new Date('. $timestamp.' * 1000), y: '.$analyzerResult->overallErrors.'}';
            $splines[$baselinePath][ResultPrinter::KEY_CLASSES_COMPLEXITY][] = '{x: new Date('. $timestamp.' * 1000), y: '.$analyzerResult->classesComplexity.'}';
            $splines[$baselinePath][ResultPrinter::KEY_DEPRECATIONS][] = '{x: new Date('. $timestamp.' * 1000), y: '.$analyzerResult->deprecations.'}';
            $splines[$baselinePath][ResultPrinter::KEY_INVALID_PHPDOCS][] = '{x: new Date('. $timestamp.' * 1000), y: '.$analyzerResult->invalidPhpdocs.'}';
            $splines[$baselinePath][ResultPrinter::KEY_UNKNOWN_TYPES][] = '{x: new Date('. $timestamp.' * 1000), y: '.$analyzerResult->unknownTypes.'}';
            $splines[$baselinePath][ResultPrinter::KEY_ANONYMOUS_VARIABLES][] = '{x: new Date('. $timestamp.' * 1000), y: '.$analyzerResult->anonymousVariables.'}';
        }

        $chartsHtml = '';
        foreach($splines as $baselinePath => $data) {
            $jsData = [];
            foreach ($data as $name => $dataPoints) {
                $type = 'line';
                $complexityProps = '';
                $axisYIndex = 0;
                if ($name == ResultPrinter::KEY_CLASSES_COMPLEXITY) {
                    $type = 'spline';
                    $complexityProps = 'color: "' . self::COMPLEXITY_COLOR . '", lineThickness: 4, axisYType: "secondary",';
                    $axisYIndex = 1;
                }

                $jsData[] = '{
                    type: "' . $type . '",
                    name: "' . $name . '",
                    axisYIndex: ' . $axisYIndex . ',
                    ' . $complexityProps . '
                    showInLegend: true,
                    xValueType: "dateTime",
                    dataPoints: [' . implode(',', $dataPoints) . ']
                }';
            }
            $chartsHtml .= '
                    <div id="chartContainer'. md5($baselinePath).'" style="height: 370px; width: 100%; margin-bottom: 30px;"></div>
                    <script>
                    (function () {
                        var chart = new CanvasJS.Chart("chartContainer'. md5($baselinePath).'", {
                            animationEnabled: true,
                            title:{
                                text: "PHPStan Baseline Analysis '. $baselinePath .'"
                            },
                                toolTip: {
                                shared: true
                            },
                            axisX: {
                                valueFormatString: "HH:mm - DD MMM YYYY"
                            },
                            axisY: {
                                title: "Number of issues",
                                complexityProps: "",
                            }, 
                            axisY2: {
                                title: "Complexity",
                                lineColor: "'. self::COMPLEXITY_COLOR .'",
                                tickColor: "'. self::COMPLEXITY_COLOR .'",
                                labelFontColor: "'. self::COMPLEXITY_COLOR .'",
                                titleFontColor: "'. self::COMPLEXITY_COLOR .'",
                            },
                            data: ['. implode(',', $jsData).']
                        });
            
                        chart.render();
                    })();
                </script>
';
        }

        return '
            <!DOCTYPE HTML>
            <html>
            <head></head>
            <body>
                <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
                '. $chartsHtml.'
            </body>
            </html>
        ';
    }
}