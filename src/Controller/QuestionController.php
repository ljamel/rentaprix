<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\VariableFee;
use App\Form\VariableFeeType;
use App\Repository\VariableFeeRepository;
use Symfony\Component\HttpFoundation\Request;

#[Route('/dashboard/question')]
class QuestionController extends AbstractController
{
    #[Route('/', name: 'app_question_index', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        return $this->renderForm('question/index.html.twig');
    }

}
