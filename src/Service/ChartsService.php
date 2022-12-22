<?php

namespace App\Service;

use App\Entity\Calcul;
use App\Entity\User;
use App\Repository\CalculRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartsService {
    public function __construct(
        private ChartBuilderInterface $chartBuilder,
        private CalculRepository $calculRepository
    ){}
    public function getDevisCharts(User $user): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $calculs = $this->calculRepository->findCalculsByUser($user->getId());
        $data = [];
        $titles = [];

        foreach ($calculs as $calcul) {
            $data[] = $calcul->getDevis();
            $titles[] = $calcul->getTitle();
        }

        $chart->setData([
            'labels' => $titles,
            'datasets' => [
                [
                    'label' => 'Devis',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $data,
                    'type'=> 'line',
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [

                ],
            ],
        ]);

        $chart->setAttributes([
            'plugin' => 'ChartDataLabels'
        ]);

        return $chart;
    }

    public function getProfitCharts(User $user): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $calculs = $this->calculRepository->findCalculsByUser($user->getId());
        $data = [];
        $titles = [];

        foreach ($calculs as $calcul) {
            $data[] = $this->calculateProfit($calcul);
            $titles[] = $calcul->getTitle();
        }

        $chart->setData([
            'labels' => $titles,
            'datasets' => [
                [
                    'label' => 'Bénéfices',
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(201, 203, 207, 0.3)'
                    ],
                    'borderColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ],
                    'borderWidth' => 1,
                    'data' => $data,
                    'type'=> 'bar',
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
            ],
        ]);

        return $chart;
    }

    public function getProfitChartsPercentage(User $user): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $calculs = $this->calculRepository->findCalculsByUser($user->getId());
        $data = [];
        $titles = [];

        foreach ($calculs as $calcul) {
            $data[] = number_format($this->calculateProfit($calcul) / $calcul->getDevis(), 2) * 100;
            $titles[] = $calcul->getTitle();
        }

        $chart->setData([
            'labels' => $titles,
            'datasets' => [
                [
                    'label' => 'Taux de rentabilité',
                    'backgroundColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        "#3e65cd",
                        "#8e5fa2",
                        "#9cba9f",
                        "#e6c3b9",
                        "#c35850"
                    ],
                    'borderColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        "#3e65cd",
                        "#8e5fa2",
                        "#9cba9f",
                        "#e6c3b9",
                        "#c35850"
                    ],
                    'borderWidth' => 1,
                    'data' => $data,
                    'type'=> 'doughnut',
                    'dataLabels' => $titles
                ],

            ],
        ]);
        $chart->setOptions([
            'responsive' => true,
            'scales' => [
                ],
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' =>'Taux de rentabilité',
                    'font' => [
                        'size' => 16,
                    ],
                    'color' => '#1761a0',
                    'position' => 'top',
                    'padding' => 10
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'right'
                ],
                'devicePixelRatio' => '1:5'
            ]
        ]);

        return $chart;
    }

    public function getCalculsPerMonth(User $user): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $calculs = $this->calculRepository->countByMonth($user->getId());
        $data = [];
        $titles = [];
        setlocale(LC_TIME, "fr_FR");

        foreach ($calculs as $calcul) {
            $data[] = $calcul['count'];
            $titles[] =
                str_replace(
                    array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
                    array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'),
                    date('F', mktime(0, 0, 0, $calcul['dateCalcul'], 10))
                );
        }

        $chart->setData([
            'labels' => $titles,
            'datasets' => [
                [
                    'label' => 'Nombre de cacluls par mois',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $data,
                    'type'=> 'bar',
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                ],
                'x' => [
                    'suggestedMin' => 0,
                ],
            ],
        ]);

        $chart->setAttributes([
            'plugin' => 'ChartDataLabels'
        ]);

        return $chart;
    }

    private function calculateProfit(Calcul $calcul) {
        return $calcul->getDevis()
            - $this->calculateTotalFees($calcul->getFixedFees()->getValues())
            - $this->calculateTotalFees($calcul->getVariableFees()->getValues())
            - ($this->calculateTotalSalaries($calcul->getSalaries()->getValues()) * $calcul->getDurationMonth());
    }

    private function calculateTotalFees(array $fees) {
        $result = 0;
        foreach ($fees as $fee) {
            $result += $fee->getPrice();
        }

        return $result;
    }

    private function calculateTotalSalaries(array $salaries) {
        $result = 0;

        foreach ($salaries as $salary) {
            $result += $salary->getPay();
        }

        return $result;
    }
}