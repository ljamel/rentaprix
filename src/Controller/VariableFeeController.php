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
    public function index(VariableFeeRepository $variableFeeRepository, UserRepository $userRepository): Response
    {
        // show information calcul relation with current user
        $userCalculs = $userRepository->findByFeeUser($this->getUser()->getId());

        return $this->render('variable_fee/index.html.twig', [
            'variable_fees' => $variableFeeRepository->findAll(),
            'userCalculs' => $userCalculs,
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
        return $this->render('variable_fee/show.html.twig', [
            'variable_fee' => $variableFee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_variable_fee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, VariableFee $variableFee, VariableFeeRepository $variableFeeRepository): Response
    {
        $form = $this->createForm(VariableFeeType::class, $variableFee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $variableFeeRepository->save($variableFee, true);

            return $this->redirectToRoute('app_variable_fee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('variable_fee/edit.html.twig', [
            'variable_fee' => $variableFee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_variable_fee_delete', methods: ['POST'])]
    public function delete(Request $request, VariableFee $variableFee, VariableFeeRepository $variableFeeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$variableFee->getId(), $request->request->get('_token'))) {
            $variableFeeRepository->remove($variableFee, true);
        }

        return $this->redirectToRoute('app_variable_fee_index', [], Response::HTTP_SEE_OTHER);
    }
}
