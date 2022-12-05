<?php

namespace App\Form;

use App\Entity\Calcul;
use App\Entity\FixedFee;
use App\Entity\Salary;
use App\Entity\VariableFee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType as TypeIntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Callback as ConstraintsCallback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;
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
                            'message' => 'Please enter a title',
                        ]),
                        new Length([
                            'min' => 3,
                            'minMessage' => 'Your title should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 256,
                        ]),
                        new Regex([
                                'pattern'=> '/^[a-z_ -]+$/i',
                                'htmlPattern'=> '^[a-zA-Z]+$',
                                'message' => 'Title must contains only letters'
                        ])
                    ],
                    'label'=> 'Titre'
                ])
            ->add('devis', MoneyType::class, 
                [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a Devis',
                        ]),
                        new Positive([
                            'message' => 'Devis must be grater than 0'
                        ]),
                    ],
                'label'=> 'Devis',
                'currency' =>''
                ])
            ->add('durationMonth', TypeIntegerType::class, 
                [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a duration month',
                        ]),
                        new Positive([
                            'message' => 'Duration month must be grater than 0'
                        ]),
                    ],
                    'label'=> 'Durée en mois'
                ])
            ->add( 'fixedFees', CollectionType::class,
                [
                    'entry_type' => FixedFeeType::class, // le formulaire enfant qui doit être répété
                    'allow_add' => true, // true si tu veux que l'utilisateur puisse en ajouter
                    'allow_delete' => true, // true si tu veux que l'utilisateur puisse en supprimer
                    'label' => 'Frais Fixes',
                    'by_reference' => false, // voir  https://symfony.com/doc/current/reference/forms/types/collection.html#by-reference
                    'data' => [new FixedFee()],
                    'required' => false
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
                            'Unit' => $fixedFee->getUnit(),  
                        ];

                        return json_encode($data);
                    },
                    'constraints' => [
                        new ConstraintsCallback([$this, 'validateFixedFees']),
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calcul::class,
            'fixedFeesChoices' => [],
            'variableFeesChoices' => [],
            'salariesChoices' => []
        ]);
    }

    public function validateFixedFees($data, ExecutionContextInterface $context): void
    {
        $fixedFees = $context->getRoot()->get('fixedFees')->getData();
        $checkedFixedFees = $context->getRoot()->get('checkedFixedFees')->getData();

        if($fixedFees[0]->getTitle() === null && count($checkedFixedFees) === 0) {
            $context->buildViolation('Select at least one fixed Fee')
                    ->addViolation();
        }
    }

    public function validateVariableFees($data, ExecutionContextInterface $context): void
    {
        $variableFees = $context->getRoot()->get('variableFees')->getData();
        $checkedVariableFees = $context->getRoot()->get('checkedVariableFees')->getData();


        if($variableFees[0]->getTitle() === null && count($checkedVariableFees) === 0) {
            $context->buildViolation('Select at least one variable Fee')
                    ->addViolation();
        }
    }

    public function validateSalaries($data, ExecutionContextInterface $context): void
    {
        $salaries = $context->getRoot()->get('salaries')->getData();
        $checkedSalaries = $context->getRoot()->get('checkedSalaries')->getData();


        if($salaries[0]->getFullName() === null && count($checkedSalaries) === 0) {
            $context->buildViolation('Select at least one salary')
                    ->addViolation();
        }
    }
}
