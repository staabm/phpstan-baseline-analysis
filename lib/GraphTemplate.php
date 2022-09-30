<?php

namespace staabm\PHPStanBaselineAnalysis;

use Iterator;

final class GraphTemplate
{
    /**
     * @param Iterator<string, array{string, AnalyzerResult}> $it
     */
    public function render(Iterator $it):string {
        $splines = [];
        $splines[ResultPrinter::KEY_OVERALL_ERRORS] = [];
        $splines[ResultPrinter::KEY_CLASSES_COMPLEXITY] = [];
        $splines[ResultPrinter::KEY_DEPRECATIONS] = [];
        $splines[ResultPrinter::KEY_INVALID_PHPDOCS] = [];
        $splines[ResultPrinter::KEY_UNKNOWN_TYPES] = [];
        $splines[ResultPrinter::KEY_ANONYMOUS_VARIABLES] = [];
        foreach($it as $fileName => $data) {
            /** @var AnalyzerResult $analyzerResult */
            list($baselinePath, $analyzerResult) = $data;
            // XXX timestamp aus result verwenden
            $timestamp = pathinfo($fileName, PATHINFO_FILENAME);

            $splines[ResultPrinter::KEY_OVERALL_ERRORS][] = '{x: new Date('. $timestamp.' * 1000), y: '.$analyzerResult->overallErrors.'}';
            $splines[ResultPrinter::KEY_CLASSES_COMPLEXITY][] = '{x: new Date('. $timestamp.' * 1000), y: '.$analyzerResult->classesComplexity.'}';
            $splines[ResultPrinter::KEY_DEPRECATIONS][] = '{x: new Date('. $timestamp.' * 1000), y: '.$analyzerResult->deprecations.'}';
            $splines[ResultPrinter::KEY_INVALID_PHPDOCS][] = '{x: new Date('. $timestamp.' * 1000), y: '.$analyzerResult->invalidPhpdocs.'}';
            $splines[ResultPrinter::KEY_UNKNOWN_TYPES][] = '{x: new Date('. $timestamp.' * 1000), y: '.$analyzerResult->unknownTypes.'}';
            $splines[ResultPrinter::KEY_ANONYMOUS_VARIABLES][] = '{x: new Date('. $timestamp.' * 1000), y: '.$analyzerResult->anonymousVariables.'}';
        }

        $jsData = [];
        foreach($splines as $name => $dataPoints) {
            $lineColor = '';
            $axisYIndex = 0;
            if ($name == ResultPrinter::KEY_CLASSES_COMPLEXITY) {
                $lineColor = '#369EAD';
                $axisYIndex = 1;
            }

            $jsData[] = '{
                type:"spline",
                name: "'.$name.'",
                axisYIndex: '. $axisYIndex.',
                lineColor: "'. $lineColor .'",
                showInLegend: true,
                xValueType: "dateTime",
                dataPoints: ['. implode(',', $dataPoints) .']
            }';
        }

        return '
            <!DOCTYPE HTML>
            <html>
            <head>
                <script>
                    window.onload = function () {
            
                        var chart = new CanvasJS.Chart("chartContainer", {
                            animationEnabled: true,
                            title:{
                                text: "PHPStan Baseline Analysis"
                            },
                            	toolTip: {
                                shared: true
                            },
                            axisX: {
                                valueFormatString: "DD MMM YYYY"
                            },
                            axisY: {
                                title: "Number of issues",
                                lineColor: "",
                            }, 
                            axisY2: {
                                title: "Complexity",
                                lineColor: "#369EAD",
                            },
                            data: ['. implode(',', $jsData).']
                        });
            
                        chart.render();
            
                    }
                </script>
            </head>
            <body>
            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
            <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
            </body>
            </html>
        ';
    }
}