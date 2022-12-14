<?php

namespace App\Controller;

use App\Entity\FixedFee;
use App\Form\FixedFeeType;
use App\Repository\FixedFeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

#[Route('/dashboard/fixed/fee')]
class FixedFeeController extends AbstractController
{
    #[Route('/', name: 'app_fixed_fee_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $page < 1 ? $page = 1: '';
        
        $fixedFees = $userRepository->findFixedFeesByUserPaginated($page, $this->getUser()->getId(), 6);

        return $this->render('fixed_fee/index.html.twig', [
            'fixedFees' => $fixedFees,
        ]);
    }

    #[Route('/new', name: 'app_fixed_fee_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FixedFeeRepository $fixedFeeRepository): Response
    {
        $fixedFee = new FixedFee();
        $form = $this->createForm(FixedFeeType::class, $fixedFee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fixedFeeRepository->save($fixedFee, true);

            $this->addFlash('success', "Le frais fixe a été ajouté avec succèes");

            return $this->redirectToRoute('app_fixed_fee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fixed_fee/new.html.twig', [
            'fixed_fee' => $fixedFee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fixed_fee_show', methods: ['GET'])]
    public function show(FixedFee $fixedFee, UserRepository $userRepository): Response
    {
        if ($userRepository->findUserByFixedFee($fixedFee->getId())[0] !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('fixed_fee/show.html.twig', [
            'fixed_fee' => $fixedFee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fixed_fee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FixedFee $fixedFee, FixedFeeRepository $fixedFeeRepository, UserRepository $userRepository): Response
    {
        if ($userRepository->findUserByFixedFee($fixedFee->getId())[0] !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(FixedFeeType::class, $fixedFee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fixedFeeRepository->save($fixedFee, true);

            $this->addFlash('success', "Le frais fixe a été modifé avec succèes");
            return $this->redirectToRoute('app_fixed_fee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fixed_fee/edit.html.twig', [
            'fixed_fee' => $fixedFee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fixed_fee_delete', methods: ['POST'])]
    public function delete(Request $request, FixedFee $fixedFee, FixedFeeRepository $fixedFeeRepository, UserRepository $userRepository): Response
    {
        if ($userRepository->findUserByFixedFee($fixedFee->getId())[0] !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$fixedFee->getId(), $request->request->get('_token'))) {
            $fixedFeeRepository->remove($fixedFee, true);
        }

        $this->addFlash('success', "Le frais fixe a été supprimé avec succèes");
        return $this->redirectToRoute('app_fixed_fee_index', [], Response::HTTP_SEE_OTHER);
    }
}
