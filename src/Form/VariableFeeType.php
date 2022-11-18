<?php

namespace App\Form;

use App\Entity\VariableFee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;

class VariableFeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
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
            ->add('price', MoneyType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a Price',
                    ]),
                    new Positive([
                        'message' => 'Price must be grater than 0'
                    ]),
                ],
            'divisor' => 100,
            'label'=> 'Prix'
            ])
            ->add('unit', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a unit',
                    ]),
                ],
                'label'=> 'Unité'
            ])
            ->add('type')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VariableFee::class,
        ]);
    }
}
