<?php

namespace App\Controller;

use App\Entity\Calcul;
use App\Form\CalculType;
use App\Repository\CalculRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/dashboard/calcul')]
class CalculController extends AbstractController
{
    #[Route('/', name: 'app_calcul_index', methods: ['GET'])]
    public function index(CalculRepository $calculRepository): Response
    {
        return $this->render('calcul/index.html.twig', [
            'calculs' => $calculRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_calcul_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CalculRepository $calculRepository, ManagerRegistry $doctrine): Response
    {
        $calcul = new Calcul();
        $form = $this->createForm(CalculType::class, $calcul);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // add current user relation in the table calcul
            $calcul->addUser($this->getUser());
            
            $calculRepository->save($calcul, true);

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
        return $this->render('calcul/show.html.twig', [
            'calcul' => $calcul,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_calcul_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calcul $calcul, CalculRepository $calculRepository): Response
    {
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
        if ($this->isCsrfTokenValid('delete'.$calcul->getId(), $request->request->get('_token'))) {
            $calculRepository->remove($calcul, true);
        }

        return $this->redirectToRoute('app_calcul_index', [], Response::HTTP_SEE_OTHER);
    }
}
