<?php

namespace App\Controller;

use App\Entity\VariableFee;
use App\Form\VariableFeeType;
use App\Repository\VariableFeeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/dashboard/variable/fee')]
class VariableFeeController extends AbstractController
{
    #[Route('/', name: 'app_variable_fee_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $page < 1 ? $page = 1: '';
        
        $variableFees = $userRepository->findVariableFeesByUserPaginated($page, $this->getUser()->getId(), 6);

        return $this->render('variable_fee/index.html.twig', [
            'variableFees' => $variableFees,
        ]);
    }

    #[Route('/new', name: 'app_variable_fee_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VariableFeeRepository $variableFeeRepository): Response
    {
        $variableFee = new VariableFee();
        $form = $this->createForm(VariableFeeType::class, $variableFee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $variableFeeRepository->save($variableFee, true);

            $this->addFlash('success', "Le frais variable a été ajouté avec succèes");

            return $this->redirectToRoute('app_variable_fee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('variable_fee/new.html.twig', [
            'variable_fee' => $variableFee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_variable_fee_show', methods: ['GET'])]
    public function show(VariableFee $variableFee, UserRepository $userRepository, int $id): Response
    {
        if ($userRepository->findUserByVariableFee($variableFee->getId())[0] !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('variable_fee/show.html.twig', [
            'variable_fee' => $variableFee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_variable_fee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, VariableFee $variableFee, VariableFeeRepository $variableFeeRepository, UserRepository $userRepository): Response
    {
        if ($userRepository->findUserByVariableFee($variableFee->getId())[0] !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(VariableFeeType::class, $variableFee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $variableFeeRepository->save($variableFee, true);
            $this->addFlash('success', "Le frais variable a été modifé avec succèes");

            return $this->redirectToRoute('app_variable_fee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('variable_fee/edit.html.twig', [
            'variable_fee' => $variableFee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_variable_fee_delete', methods: ['POST'])]
    public function delete(Request $request, VariableFee $variableFee, VariableFeeRepository $variableFeeRepository, UserRepository $userRepository): Response
    {
        if ($userRepository->findUserByVariableFee($variableFee->getId())[0] !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$variableFee->getId(), $request->request->get('_token'))) {
            $variableFeeRepository->remove($variableFee, true);
        }

        $this->addFlash('success', "Le frais variable a été supprimé avec succèes");
        return $this->redirectToRoute('app_variable_fee_index', [], Response::HTTP_SEE_OTHER);
    }
}
