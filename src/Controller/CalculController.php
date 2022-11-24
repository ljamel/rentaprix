<?php

namespace App\Controller;

use App\Entity\Calcul;
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
    public function new(Request $request, CalculRepository $calculRepository, UserRepository $userRepository): Response
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
        
        if ($form->isSubmitted() && $form->isValid()) {         
            $checkedFixedFees = $form->get('checkedFixedFees')->getData();
            $createdFixedFees = $form->get('fixedFees')->getData();

            $checkedVariableFees = $form->get('checkedVariableFees')->getData();
            $createdVariableFees = $form->get('variableFees')->getData();

            $checkedSalaries = $form->get('checkedSalaries')->getData();
            $createdSalaries = $form->get('salaries')->getData();
            
            $calcul->addFixedFees($checkedFixedFees, $createdFixedFees);
            $calcul->addVariableFees($checkedVariableFees, $createdVariableFees);
            $calcul->addSalaries($checkedSalaries, $createdSalaries);

            $calcul->addUser($this->getUser());
            
            $calculRepository->save($calcul, true);
            
            $this->addFlash('sucess', 'Le calcul a été crée avec succées');

            return $this->redirectToRoute('app_calcul_index', [], Response::HTTP_SEE_OTHER); 
        }
        
        return $this->renderForm('calcul/new.html.twig', [
            'calcul' => $calcul,
            'form' => $form,
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
