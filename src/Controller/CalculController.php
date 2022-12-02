<?php

namespace App\Controller;

use App\Entity\Calcul;
use App\Service\CalculService;
use App\Form\CalculType;
use App\Repository\CalculRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/calcul')]
class CalculController extends AbstractController
{
    #[Route('/', name: 'app_calcul_index', methods: ['GET'])]
    public function index(CalculRepository $calculRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $page < 1 ? $page = 1: '';

        $userCalculs = $calculRepository->findCalculsByUserPaginated($page, 6, $this->getUser()->getId());
        
        return $this->render('calcul/index.html.twig', [
            'userCalculs' => $userCalculs,
        ]);
    }

    #[Route('/new', name: 'app_calcul_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, CalculService $calculService): Response
    {
        $calcul = new Calcul();
        $page = $request->query->getInt('page', 1);

        $page < 1 ? $page = 1: '';

        $oldFixedFees = $userRepository->findFixedFeesByUserPaginated($page, 5, $this->getUser()->getId());
        $oldVariableFees = $userRepository->findVariableFeesByUserPaginated($page, 5, $this->getUser()->getId());
        $oldSalaries = $userRepository->findSalariesByUserPaginated($page, 5, $this->getUser()->getId());

        $form = $this->createForm(CalculType::class, $calcul, [
            'fixedFeesChoices' => $oldFixedFees,
            'variableFeesChoices' => $oldVariableFees,
            'salariesChoices' => $oldSalaries,
        ]);
        
        $form->handleRequest($request);
    
        
        if ($form->isSubmitted()) {  
            return $calculService->handleCalculFormData($form, $this->getUser(), $request->get('tab'));
        }
        
        return $this->renderForm('calcul/new.html.twig', [
            'calcul' => $calcul,
            'form' => $form,
            'numberOfPagesFixed' => $oldFixedFees['pages'],
            'currentPageFixed' => $oldFixedFees['page'],
            'numberOfPagesVariable' => $oldVariableFees['pages'],
            'currentPageVariable' => $oldVariableFees['page'],
            'numberOfPagesSalary' => $oldSalaries['pages'],
            'currentPageSalary' => $oldSalaries['page'],
        ]);
    }

    #[Route('/{id}', name: 'app_calcul_show', methods: ['GET'])]
    public function show(Calcul $calcul): Response
    {
        if ($calcul->getUser()->get(0) !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        
        return $this->render('calcul/show.html.twig', [
            'calcul' => $calcul,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_calcul_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calcul $calcul, CalculRepository $calculRepository): Response
    {
        if ($calcul->getUser()->get(0) !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(CalculType::class, $calcul);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $calculRepository->save($calcul, true);

            return $this->redirectToRoute('app_calcul_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('calcul/edit.html.twig', [
            'calcul' => $calcul,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calcul_delete', methods: ['POST'])]
    public function delete(Request $request, Calcul $calcul, CalculRepository $calculRepository): Response
    {
        if ($calcul->getUser()->get(0) !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$calcul->getId(), $request->request->get('_token'))) {
            $calculRepository->remove($calcul, true);
        }

        return $this->redirectToRoute('app_calcul_index', [], Response::HTTP_SEE_OTHER);
    }
}
