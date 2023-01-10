<?php

namespace App\Form;

use App\Entity\Calcul;
use App\Entity\FixedFee;
use App\Entity\FixedFeeCalcul;
use App\Entity\Salary;
use App\Entity\VariableFee;
use Doctrine\DBAL\Types\IntegerType;
use http\Message;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Callback as ConstraintsCallback;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CalculType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fixedFeesChoices = $options['fixedFeesChoices'];
        $variableFeesChoices = $options['variableFeesChoices'];
        $salariesChoices= $options['salariesChoices'];

        $builder
            ->add('title', TextType::class,
                [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Merci de renseigner ce champs',
                        ]),
                        new Length([
                            'min' => 3,
                            'minMessage' => 'Le titre doit contenir au moins {{ limit }} charactères',
                            'max' => 256,
                        ]),

                    ],
                    'label'=> 'Titre'
                ])
            ->add('devis', MoneyType::class,
                [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Merci de renseigner ce champs',
                        ]),
                        new Positive([
                            'message' => 'La devis doit être supérieur à zéro'
                        ]),
                    ],
                    'label'=> 'Budget client',
                    'currency' =>'',
                    'invalid_message' => 'Cette valeur n\'est pas valide'
                ])
            ->add('startDate', DateTimeType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'label' => 'Date de début',
                    'widget' => 'choice',
                    'html5' => false,
                    'date_format' => "ddMMyyyy",
                    'view_timezone' => date_default_timezone_get() // Europe/Paris
                ])
            ->add('endDate', DateTimeType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new GreaterThan([
                            'propertyPath' => 'parent.all[startDate].data',
                            'message' => "La date de fin doit être supérieure à la date de début"
                        ]),
                    ],
                    'label' => 'Date de fin',
                    'widget' => 'choice',
                    'html5' => false,
                    'date_format' => "ddMMyyyy",
                    'view_timezone' => date_default_timezone_get() // Europe/Paris
                ])
            ->add('software', MoneyType::class,
                [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Merci de renseigner ce champs',
                        ]),
                        new PositiveOrZero([
                            'message' => 'La valeur doit être supérieure ou égale à zéro'
                        ]),
                    ],
                    'label'=> 'Logiciels',
                    'currency' =>'',
                    'invalid_message' => 'Cette valeur n\'est pas valide'
                ])
            ->add('hardware', MoneyType::class,
                [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Merci de renseigner ce champs',
                        ]),
                        new PositiveOrZero([
                            'message' => 'La valeur doit être supérieure ou égale à zéro'
                        ]),
                    ],
                    'label'=> 'Matériels',
                    'currency' =>'',
                    'invalid_message' => 'Cette valeur n\'est pas valide'
                ])
            ->add('training', MoneyType::class,
                [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Merci de renseigner ce champs',
                        ]),
                        new PositiveOrZero([
                            'message' => 'La valeur doit être supérieure ou égale à zéro'
                        ]),
                    ],
                    'label'=> 'Formation',
                    'currency' =>'',
                    'invalid_message' => 'Cette valeur n\'est pas valide'
                ])
            ->add('startupExpenses', MoneyType::class,
                [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Merci de renseigner ce champs',
                        ]),
                        new PositiveOrZero([
                            'message' => 'La valeur doit être supérieure ou égale à zéro'
                        ]),
                    ],
                    'label'=> 'Dépenses de démarrage',
                    'currency' =>'',
                    'invalid_message' => 'Cette valeur n\'est pas valide'
                ])
            ->add('fixedFeeCalculs', CollectionType::class,
                [
                    'entry_type' => FixedFeeCalculType::class, // le formulaire enfant qui doit être répété
                    'allow_add' => true, // true si tu veux que l'utilisateur puisse en ajouter
                    'allow_delete' => true, // true si tu veux que l'utilisateur puisse en supprimer
                    'label' => 'Frais Fixes',
                    'by_reference' => false, // voir  https://symfony.com/doc/current/reference/forms/types/collection.html#by-reference
                    'data' => [new FixedFeeCalcul()],
                    'required' => false,
                ]
            )
            ->add('variableFees', CollectionType::class,
                [
                    'entry_type' => VariableFeeType::class, // le formulaire enfant qui doit être répété
                    'allow_add' => true, // true si tu veux que l'utilisateur puisse en ajouter
                    'allow_delete' => true, // true si tu veux que l'utilisateur puisse en supprimer
                    'label' => 'Frais variables',
                    'by_reference' => false, // voir  https://symfony.com/doc/current/reference/forms/types/collection.html#by-reference
                    'data' => [new VariableFee()],
                    'required' => false
                ]
            )
            ->add( 'salaries', CollectionType::class,
                [
                    'entry_type' => SalaryType::class, // le formulaire enfant qui doit être répété
                    'allow_add' => true, // true si tu veux que l'utilisateur puisse en ajouter
                    'allow_delete' => true, // true si tu veux que l'utilisateur puisse en supprimer
                    'label' => 'Salariés',
                    'by_reference' => false, // voir  https://symfony.com/doc/current/reference/forms/types/collection.html#by-reference
                    'data' => [new Salary()],
                    'required' => false
                ]
            )
            ->add('checkedFixedFees', EntityType::class,
                [
                    'class' => FixedFee::class,
                    'multiple' => true,
                    'mapped' => false,
                    'expanded' => true,
                    'choices' => $fixedFeesChoices,
                    'choice_label' => function (FixedFee $fixedFee) {
                        $data = [
                            'Title' => $fixedFee->getTitle(),
                            'Price' => $fixedFee->getPrice(),
                            'quantity' => $fixedFee->getFixedFeeCalculs()->get(0)->getQuantity()
                        ];

                        return json_encode($data);
                    },
                    'constraints' => [
                        new ConstraintsCallback([$this, 'validateFixedFees']),
                    ],
                ]
            )
            ->add('fixedFeeCalculsQuantity', CollectionType::class,
                [
                    'data' => [new FixedFeeCalcul()],
                    'mapped' => false,
                    'entry_type' =>FixedFeeCalculType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'constraints' => [
                        new ConstraintsCallback([$this, 'validateQuantities']),
                    ],
                ]
            )
            ->add('checkedVariableFees', EntityType::class,
                [
                    'class' => VariableFee::class,
                    'multiple' => true,
                    'mapped' => false,
                    'expanded' => true,
                    'choices' => $variableFeesChoices,
                    'choice_label' => function (VariableFee $variableFee) {
                        $data = [
                            'Title' => $variableFee->getTitle(),
                            'Price' => $variableFee->getPrice(),
                            'Unit' => $variableFee->getUnit(),
                            'Type' => $variableFee->getType(),
                        ];
                        return json_encode($data);
                    },
                    'constraints' => [
                        new ConstraintsCallback([$this, 'validateVariableFees']),
                    ],
                ]
            )
            ->add('checkedSalaries', EntityType::class,
                [
                    'class' => Salary::class,
                    'multiple' => true,
                    'mapped' => false,
                    'expanded' => true,
                    'choices' => $salariesChoices,
                    'choice_label' => function (Salary $salary) {
                        $data = [
                            'Nom et Prénom' => $salary->getFullName(),
                            'Poste' => $salary->getPost(),
                            'Rémunération' => $salary->getPay(),
                        ];
                        return json_encode($data);
                    },
                    'constraints' => [
                        new ConstraintsCallback([$this, 'validateSalaries']),
                    ],
                ]
            )
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'save'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calcul::class,
            'fixedFeesChoices' => [],
            'variableFeesChoices' => [],
            'salariesChoices' => [],
        ]);
    }

    public function validateQuantities($data, ExecutionContextInterface $context): void
    {
        $fixedFeeCalculs = $context->getRoot()->get('fixedFeeCalculsQuantity')->getData();

        if(!empty($fixedFeeCalculs)) {
            foreach ($fixedFeeCalculs as $quantity) {
                if($quantity->getQuantity() <= 0) {
                    $context->buildViolation('La quantité doit etre supérieure ou égale à 0')
                        ->atPath('checkedFixedFees')
                            ->addViolation();
                }
            }
       }
    }

    public function validateFixedFees($data, ExecutionContextInterface $context): void
    {
        $fixedFeeCalculs = $context->getRoot()->get('fixedFeeCalculs')->getData();
        $checkedFixedFeeCalculs = $context->getRoot()->get('checkedFixedFees')->getData();

        //if($fixedFees[0]->getTitle() === null && count($checkedFixedFees) === 0) {// à corriger
        //  $context->buildViolation('Veuillez ajouter un coût récurrent')
        //    ->addViolation();
        //}
    }

    public function validateVariableFees($data, ExecutionContextInterface $context): void
    {
        $variableFees = $context->getRoot()->get('variableFees')->getData();
        $checkedVariableFees = $context->getRoot()->get('checkedVariableFees')->getData();


        if($variableFees[0]->getTitle() === null && count($checkedVariableFees) === 0) {
            $context->buildViolation('Veuillez ajouter un coût ponctuel')
                ->addViolation();
        }
    }

    public function validateSalaries($data, ExecutionContextInterface $context): void
    {
        $salaries = $context->getRoot()->get('salaries')->getData();
        $checkedSalaries = $context->getRoot()->get('checkedSalaries')->getData();


        if($salaries[0]->getFullName() === null && count($checkedSalaries) === 0) {
            $context->buildViolation('Veuillez ajouter un salarié')
                ->addViolation();
        }
    }
}