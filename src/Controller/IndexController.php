<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {

        return $this->render('index/index.html.twig', [
            'controller_name' => 'Rentaprix',
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {

        return $this->render('index/about.html.twig');
    }

    #[Route('/mentions-legales', name: 'app_mentions-legales')]
    public function mentions(): Response
    {

        return $this->render('index/mentions.html.twig');
    }

    #[Route('/condition-generales', name: 'app_condition-generales')]
    public function condition(): Response
    {

        return $this->render('index/condition-subscribe.html.twig');
    }

    #[Route('/confidentialites', name: 'app_confidentialites')]
    public function confidentialites(): Response
    {

        return $this->render('index/confidentialite.html.twig');
    }
}
