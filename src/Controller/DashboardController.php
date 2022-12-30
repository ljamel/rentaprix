<?php

namespace App\Controller;

use App\Service\ChartsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(ChartsService $chartsService): Response
    {
        // check if subscribe
        if ($this->getUser()->getSubscribeId() == null) {
            return $this->render('registration/payement.html.twig');
        }

        $devisChart = $chartsService->getDevisCharts($this->getUser());
        $profitChart = $chartsService->getProfitCharts($this->getUser());
        $profitPercentage = $chartsService->getProfitChartsPercentage($this->getUser());
        $calculsPerMonth = $chartsService->getCalculsPerMonth($this->getUser());
        
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'devisChart' => $devisChart,
            'profitChart' =>$profitChart,
            'profitPercentage' => $profitPercentage,
            'caclulsPerMonth' => $calculsPerMonth,
        ]);
    }
}
