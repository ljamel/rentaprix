<?php

namespace App\Service;

use App\Entity\Calcul;
use App\Entity\User;
use App\Repository\CalculRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;

Class CalculService {
    public function __construct(
        private CalculRepository $calculRepository,
        private Environment $environment
    ) {}

    public function handleCalculFormData(FormInterface $calculForm, User $user, int $tab=null): JsonResponse
    {
        if ($tab === 0) {
            if (!$calculForm->get('title')->isValid() || !$calculForm->get('devis')->isValid() || !$calculForm->get('durationMonth')->isValid()) {
                return $this->handleInvalidForm($calculForm, 0);
            }
        } 
        
        if($tab === 1){
            if (!$calculForm->get('checkedFixedFees')->isValid()) {
                return $this->handleInvalidForm($calculForm, 1);
            }
        }
        
        if($tab === 2){
            if (!$calculForm->get('checkedVariableFees')->isValid()) {
                return $this->handleInvalidForm($calculForm, 2);
            }
        } 
        
        if($tab === 3){
            if (!$calculForm->get('checkedSalaries')->isValid()) {
                return $this->handleInvalidForm($calculForm, 3);
            }
        } 
        
        return $this->handleValidForm($calculForm, $user);
    }

    private function handleValidForm(FormInterface $form, User $user) 
    {
        if($form->isValid()){
            $calcul = $form->getData();

            $checkedFixedFees = $form->get('checkedFixedFees')->getData();
            $createdFixedFees = $form->get('fixedFees')->getData();

            $checkedVariableFees = $form->get('checkedVariableFees')->getData();
            $createdVariableFees = $form->get('variableFees')->getData();

            $checkedSalaries = $form->get('checkedSalaries')->getData();
            $createdSalaries = $form->get('salaries')->getData();
            
            $calcul->addFixedFees($checkedFixedFees, $createdFixedFees);
            $calcul->addVariableFees($checkedVariableFees, $createdVariableFees);
            $calcul->addSalaries($checkedSalaries, $createdSalaries);

            $calcul->addUser($user);
            
            $this->calculRepository->save($calcul, true);
            
            //$this->addFlash('sucess', 'Le calcul a été crée avec succées');
            return new JsonResponse([
                'code' => Calcul::CALCUL_ADDED_SUCCESSFULLY,
                'success' => true,
            ]);
        }

        return new JsonResponse([
            'code' => Calcul::CALCUL_ADDED_SUCCESSFULLY,
        ]);
    }

    private function handleInvalidForm(FormInterface $calculForm, $tab) : JsonResponse
    {
        return new JsonResponse([
            'code' => Calcul::CALCUL_INVALID_FORM,
            'errors' => $this->getErrorMessages($calculForm),
            'tab' => $tab
        ]);
    }

    private function getErrorMessages(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
}