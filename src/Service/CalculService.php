<?php

namespace App\Service;

use App\Entity\Calcul;
use App\Entity\User;
use App\Repository\CalculRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

Class CalculService {
    public function __construct(
        private CalculRepository $calculRepository
    ) {}

    public function handleCalculFormData(FormInterface $calculForm, User $user, int $tab=null): JsonResponse
    {
        if ($tab === 0) {
            if (!$calculForm->get('title')->isValid() || !$calculForm->get('devis')->isValid()
                || !$calculForm->get('startDate')->isValid() || !$calculForm->get('endDate')->isValid()) {
                return $this->handleInvalidForm($calculForm, 0);
            }
        }

        if ($tab === 1) {
            if (!$calculForm->get('software')->isValid() || !$calculForm->get('hardware')->isValid()
                || !$calculForm->get('training')->isValid() || !$calculForm->get('startupExpenses')->isValid()) {
                return $this->handleInvalidForm($calculForm, 1);
            }
        }

        if($tab === 2 && !$calculForm->get('checkedFixedFees')->isValid()){
            return $this->handleInvalidForm($calculForm, 2);
        }

        if($tab === 3 && !$calculForm->get('checkedVariableFees')->isValid()){
            return $this->handleInvalidForm($calculForm, 3);
        }

        if($tab === 4 && !$calculForm->get('checkedSalaries')->isValid()){
            return $this->handleInvalidForm($calculForm, 4);
        }


        return $this->handleValidForm($calculForm, $user, $tab);
    }

    public function handleFees($form, $calcul): void
    {
        $checkedFixedFees = $form->get('checkedFixedFees')->getData();
        $checkedVariableFees = $form->get('checkedVariableFees')->getData();
        $checkedSalaries = $form->get('checkedSalaries')->getData();

        $calcul->addFixedFees($checkedFixedFees, $form->get('fixedFees')->getData());
        $calcul->addVariableFees($checkedVariableFees, $form->get('variableFees')->getData());
        $calcul->addSalaries($checkedSalaries, $form->get('salaries')->getData());
    }

    private function handleValidForm(FormInterface $form, User $user, int $tab): JsonResponse
    {
        if($form->isValid() && $tab === 5){
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

            return new JsonResponse([
                'code' => Calcul::CALCUL_ADDED_SUCCESSFULLY,
                'success' => true,
            ]);
        } else {
            return new JsonResponse([
                'code' => Calcul::CALCUL_ADDED_SUCCESSFULLY,
            ]);
        }
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