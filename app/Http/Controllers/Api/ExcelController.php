<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Postamat;
use App\Models\Review;
use App\Services\PostamatService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

class ExcelController extends Controller
{
    private function createSheet($spreadsheet, $name = 'sheet')
    {
        $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $name);
        $spreadsheet->addSheet($sheet);
        return $sheet;
    }

    private function formPieChart($title, $rightLegend, $dataLenegds, $data)
    {
        $title1 = new Title($title);

        // Легенды справа
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        $xAxisTickValues1 = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $rightLegend, null, 4),
        ];

        //
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        $dataSeriesLabels1 = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $dataLenegds, null, 1),
        ];

        // Данные для графика
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        $dataSeriesValues1 = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $data, null, 4),
        ];

        // Build the dataseries
        $series1 = new DataSeries(
            DataSeries::TYPE_PIECHART, // plotType
            null, // plotGrouping (Pie charts don't have any grouping)
            range(0, count($dataSeriesValues1) - 1), // plotOrder
            $dataSeriesLabels1, // plotLabel
            $xAxisTickValues1, // plotCategory
            $dataSeriesValues1 // plotValues
        );

        // Set up a layout object for the Pie chart
        $layout1 = new Layout();
        $layout1->setShowVal(true);
        $layout1->setShowPercent(true);

        // Set the series in the plot area
        $plotArea1 = new PlotArea($layout1, [$series1]);
        // Set the chart legend
        $legend1 = new ChartLegend(ChartLegend::POSITION_RIGHT, null, false);

        // Create the chart
        $chart1 = new Chart(
            'chart1', // name
            $title1, // title
            $legend1, // legend
            $plotArea1, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            null   // yAxisLabel - Pie charts don't have a Y-Axis
        );
        return $chart1;
    }

    public function dashboard_xls(Postamat $postamat = null) {
        //php artisan storage:link предварительно
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        $sheet = $this->createSheet($spreadsheet, 'Все отзывы');
        $reviews = $postamat ? $postamat->reviews()->with('postamat')->get() : PostamatService::ReviewsFull();

        $table = [
            ['id', 'postamat', 'score', 'text', 'created', 'user', 'approved', 'category', 'characteristic']
        ];
        foreach ($reviews as $review) {
            $table[] = [
                $review->id,
                $review->postamat->name,
                $review->score,
                $review->text,
                $review->created_at,
                $review->user_fio,
                $review->confirmed,
                'Категория',
                $review->characteristic,
            ];
        }

        $totalTable = [
            [
                'Характеристика',
                'Всего',
                'Курьеры',
                'Расположение',
                'Доставка'
            ],
            [
                'Нейтральных',
                $reviews->filter(function ($item) {
                    return $item->neutral;
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->neutral && $item->category === 'Курьер';
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->neutral && $item->category === 'Расположение';
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->neutral && $item->category === 'Доставка';
                })->count(),
            ],
            [
                'Негативных',
                $reviews->filter(function ($item) {
                    return $item->negative;
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->negative && $item->category === 'Курьер';
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->negative && $item->category === 'Расположение';
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->negative && $item->category === 'Доставка';
                })->count(),
            ],
            [
                'Положительных',
                $reviews->filter(function ($item) {
                    return $item->positive;
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->positive && $item->category === 'Курьер';
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->positive && $item->category === 'Расположение';
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->positive && $item->category === 'Доставка';
                })->count(),
            ]
        ];
        $sheet->setCellValue('A1', $postamat?->name);
        $sheet->fromArray($table, null, $postamat ? 'A2' : 'A1');

        $sheet2 = $this->createSheet($spreadsheet, 'Аналитика');
        $sheet2->fromArray($totalTable);

        $chart1 = $this->formPieChart('Общая оценка', '\'Аналитика\'!$A$2:$A$4', '\'Аналитика\'!$B$1', '\'Аналитика\'!$B$2:$B$4');
        $chart1->setTopLeftPosition('J1');
        $chart1->setBottomRightPosition('P12');
        $sheet2->addChart($chart1);

        $chartCour = $this->formPieChart('Работа курьеров', '\'Аналитика\'!$A$2:$A$4', '\'Аналитика\'!$B$1', '\'Аналитика\'!$C$2:$C$4');
        $chartCour->setTopLeftPosition('J13');
        $chartCour->setBottomRightPosition('P24');
        $sheet2->addChart($chartCour);

        $chartAlloc = $this->formPieChart('Удобство расположения', '\'Аналитика\'!$A$2:$A$4', '\'Аналитика\'!$B$1', '\'Аналитика\'!$D$2:$D$4');
        $chartAlloc->setTopLeftPosition('Q1');
        $chartAlloc->setBottomRightPosition('V12');
        $sheet2->addChart($chartAlloc);

        $chartSpeed = $this->formPieChart('Скорость доставки', '\'Аналитика\'!$A$2:$A$4', '\'Аналитика\'!$B$1', '\'Аналитика\'!$E$2:$E$4');
        $chartSpeed->setTopLeftPosition('Q13');
        $chartSpeed->setBottomRightPosition('V24');
        $sheet2->addChart($chartSpeed);

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $file_name = "Dashboard";
        $writer->save(public_path('/storage/excel/'.$file_name.'.xlsx'));
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        return response()->download(public_path('/storage/excel/'.$file_name.'.xlsx'));
    }
}