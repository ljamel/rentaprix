<?php

namespace App\Controller;

use App\Repository\CalculRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(ChartBuilderInterface $chartBuilder, CalculRepository $calculRepository): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $calculs =$calculRepository->findCalculsByUser($this->getUser()->getId());
        $data = [];
        $dates = [];

        foreach ($calculs as $calcul) {
            $data[] = $calcul->getDevis();
            $dates[] = $calcul->getTitle();
        }

        $chart->setData([
            'labels' => $dates,
            'datasets' => [
                [
                    'label' => 'Mes devis',
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
                    'suggestedMin' => 0,
                    'suggestedMax' => 80000,
                ],
            ],
        ]);
        
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'chart' => $chart,
        ]);
    }

}
