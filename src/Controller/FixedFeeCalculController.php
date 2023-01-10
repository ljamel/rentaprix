<?php

namespace App\Controller;

use App\Entity\FixedFeeCalcul;
use App\Form\FixedFeeCalculType;
use App\Repository\FixedFeeCalculRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/fixed/fee/calcul')]
class FixedFeeCalculController extends AbstractController
{
    #[Route('/', name: 'app_fixed_fee_calcul_index', methods: ['GET'])]
    public function index(FixedFeeCalculRepository $fixedFeeCalculRepository): Response
    {
        return $this->render('fixed_fee_calcul/index.html.twig', [
            'fixed_fee_calculs' => $fixedFeeCalculRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_fixed_fee_calcul_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FixedFeeCalculRepository $fixedFeeCalculRepository): Response
    {
        $fixedFeeCalcul = new FixedFeeCalcul();
        $form = $this->createForm(FixedFeeCalculType::class, $fixedFeeCalcul);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fixedFeeCalculRepository->save($fixedFeeCalcul, true);

            return $this->redirectToRoute('app_fixed_fee_calcul_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fixed_fee_calcul/new.html.twig', [
            'fixed_fee_calcul' => $fixedFeeCalcul,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fixed_fee_calcul_show', methods: ['GET'])]
    public function show(FixedFeeCalcul $fixedFeeCalcul): Response
    {
        return $this->render('fixed_fee_calcul/show.html.twig', [
            'fixed_fee_calcul' => $fixedFeeCalcul,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fixed_fee_calcul_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FixedFeeCalcul $fixedFeeCalcul, FixedFeeCalculRepository $fixedFeeCalculRepository): Response
    {
        $form = $this->createForm(FixedFeeCalculType::class, $fixedFeeCalcul);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fixedFeeCalculRepository->save($fixedFeeCalcul, true);

            return $this->redirectToRoute('app_fixed_fee_calcul_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fixed_fee_calcul/edit.html.twig', [
            'fixed_fee_calcul' => $fixedFeeCalcul,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fixed_fee_calcul_delete', methods: ['POST'])]
    public function delete(Request $request, FixedFeeCalcul $fixedFeeCalcul, FixedFeeCalculRepository $fixedFeeCalculRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fixedFeeCalcul->getId(), $request->request->get('_token'))) {
            $fixedFeeCalculRepository->remove($fixedFeeCalcul, true);
        }

        return $this->redirectToRoute('app_fixed_fee_calcul_index', [], Response::HTTP_SEE_OTHER);
    }
}
