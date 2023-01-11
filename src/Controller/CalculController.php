<?php

namespace App\Controller;

use App\Entity\Calcul;
use App\Form\CalculType;
use App\Repository\CalculRepository;
use App\Repository\UserRepository;
use App\Service\CalculService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/calcul')]
class CalculController extends AbstractController
{
    #[Route('/', name: 'app_calcul_index', methods: ['GET'])]
    public function index(CalculRepository $calculRepository, Request $request, CalculService $calculService): Response
    {
        $durations =  [];
        $page = $request->query->getInt('page', 1);

        $page < 1 ? $page = 1: '';

        $userCalculs = $calculRepository->findCalculsByUserPaginated($page, $this->getUser()->getId());

        if(!empty($userCalculs)) {
            foreach ($userCalculs['data'] as $calcul) {
                $durations[] = $calculService->calculateDuration($calcul->getStartDate()->format('Y-m-d H:i:s'), $calcul->getEndDate()->format('Y-m-d H:i:s'));
            }

            $userCalculs['durations'] = $durations;
        }

        return $this->render('calcul/index.html.twig', [
            'userCalculs' => $userCalculs,
        ]);
    }

    #[Route('/new', name: 'app_calcul_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, CalculService $calculService): Response
    {
        $calcul = new Calcul();

        $oldFixedFees = $userRepository->findFixedFeesByUser($this->getUser()->getId());

        $oldVariableFees = $userRepository->findVariableFeesByUser($this->getUser()->getId());
        $oldSalaries = $userRepository->findSalariesByUser($this->getUser()->getId());

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
        ]);
    }

    #[Route('/redirect', name: 'app_calcul_redirect', methods: ['GET'])]
    public function redirectToMainPage(): Response
    {
        $this->addFlash('success', "Le Calcul a été crée avec succès");
        return $this->redirectToRoute('app_calcul_index', [], Response::HTTP_SEE_OTHER);
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
    public function edit(Request $request, Calcul $calcul, 
            CalculRepository $calculRepository, CalculService $calculService, UserRepository $userRepository): Response
    {
        if ($calcul->getUser()->get(0) !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $oldFixedFees = $calcul->getFixedFeeCalculs();
        $oldVariableFees = $calcul->getVariableFees();
        $oldSalaries = $calcul->getSalaries();

        $form = $this->createForm(CalculType::class, $calcul, [
            'fixedFeeCalculChoices' => $oldFixedFees,
            'variableFeesChoices' => $oldVariableFees,
            'salariesChoices' => $oldSalaries,
        ]);
        
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $calculService->handleFees($form, $calcul);
            
            if($form->isValid()) {
                $calculRepository->save($calcul, true);

                $this->addFlash('success', "Le Calcul a été modifié avec succès");
                
                return $this->redirectToRoute('app_calcul_index', [], Response::HTTP_SEE_OTHER);
            }
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

        $this->addFlash('success', "Le Calcul a été supprimé avec succès");
        
        return $this->redirectToRoute('app_calcul_index', [], Response::HTTP_SEE_OTHER);
    }
}
