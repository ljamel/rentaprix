<?php

namespace App\Form;

use App\Entity\Calcul;
use App\Entity\FixedFee;
use App\Entity\FixedFeeCalcul;
use App\Entity\Salary;
use App\Entity\VariableFee;
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

        // Pour avoir les frais fixes avec les quantité au moment de l'édition du calcul
        $fixedFeeCalculChoices = $options['fixedFeeCalculChoices'];

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
                    'entry_type' => FixedFeeCalculType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Frais Fixes',
                    'by_reference' => false,
                    'data' => [new FixedFeeCalcul()],
                    'required' => false,
                ]
            )
            ->add('variableFees', CollectionType::class,
                [
                    'entry_type' => VariableFeeType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Frais variables',
                    'by_reference' => false,
                    'data' => [new VariableFee()],
                    'required' => false
                ]
            )
            ->add( 'salaries', CollectionType::class,
                [
                    'entry_type' => SalaryType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Salariés',
                    'by_reference' => false,
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
            //we need this var only when we edit the calcul
            ->add('fixedFeeCalculChoices', EntityType::class,
                [
                    'class' => FixedFeeCalcul::class,
                    'multiple' => true,
                    'mapped' => false,
                    'expanded' => true,
                    'choices' => $fixedFeeCalculChoices,
                    'choice_label' => function (FixedFeeCalcul $fixedFeeCalcul) {
                        $data = [
                            'Title' => $fixedFeeCalcul->getFixedFee()->getTitle(),
                            'Price' => $fixedFeeCalcul->getFixedFee()->getPrice(),
                            'quantity' => $fixedFeeCalcul->getQuantity()
                        ];

                        return json_encode($data);
                    },
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
            'fixedFeeCalculChoices' => [],
        ]);
    }

    public function validateFixedFees($data, ExecutionContextInterface $context): void
    {
        $fixedFeeCalculs = $context->getRoot()->get('fixedFeeCalculs')->getData();

        $checkedFixedFeeCalculs = $context->getRoot()->get('checkedFixedFees')->getData();

        $fixedFeeCalculChoices = $context->getRoot()->get('fixedFeeCalculChoices')->getData();

        foreach ($fixedFeeCalculs as $value) {

            if (count($checkedFixedFeeCalculs) === 0 && $value->getFixedFee() === null && count($fixedFeeCalculChoices) === 0) {
                $context->buildViolation('Veuillez ajouter un coût récurrent')
                    ->atPath('checkedFixedFees')
                    ->addViolation();
            }

            if ($value->getFixedFee() !== null && $value->getFixedFee()->getTitle() === null) {
                $context->buildViolation('Merci de renseigner le titre')
                    ->atPath('checkedFixedFees')
                    ->addViolation();
            }

            if ($value->getFixedFee() !== null && $value->getFixedFee()->getPrice() === null) {
                $context->buildViolation('Merci de renseigner le prix')
                    ->atPath('checkedFixedFees')
                    ->addViolation();
            }

            if ($value->getFixedFee() !== null && $value->getQuantity() === null) {
                $context->buildViolation('Merci de renseigner la quantité')
                    ->atPath('checkedFixedFees')
                    ->addViolation();
            }
        }
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