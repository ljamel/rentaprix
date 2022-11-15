<?php

namespace App\Form;

use App\Entity\Calcul;
use App\Entity\FixedFee;
use App\Entity\VariableFee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CalculType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = $options['choices'];
        
        $builder
            ->add('title')
            ->add('devis')
            ->add('durationMonth')
            ->add( 'fixedFees', CollectionType::class,
                [
                    'entry_type' => FixedFeeType::class, // le formulaire enfant qui doit être répété
                    'allow_add' => true, // true si tu veux que l'utilisateur puisse en ajouter
                    'allow_delete' => false, // true si tu veux que l'utilisateur puisse en supprimer
                    'label' => 'Frais Fixes',
                    'by_reference' => false, // voir  https://symfony.com/doc/current/reference/forms/types/collection.html#by-reference
                    'data' => [new FixedFee()]
                ]
            )
            ->add('variableFees', CollectionType::class,
                [
                    'entry_type' => VariableFeeType::class, // le formulaire enfant qui doit être répété
                    'allow_add' => true, // true si tu veux que l'utilisateur puisse en ajouter
                    'allow_delete' => false, // true si tu veux que l'utilisateur puisse en supprimer
                    'label' => 'Frais variables',
                    'by_reference' => true, // voir  https://symfony.com/doc/current/reference/forms/types/collection.html#by-reference
                    'data' => [new VariableFee()]
                ]
            )
            ->add('otherFees', EntityType::class, 
            [
                'class' => FixedFee::class,
                'multiple' => true,
                'mapped' => false,
                'expanded' => true,
                'choices' => $choices,
                'choice_label' => function (FixedFee $fixedFee) {
                    $data = [
                        'Title' => $fixedFee->getTitle(),
                        'Price' => $fixedFee->getPrice(),
                        'Unit' => $fixedFee->getUnit(),
                    ];
        
                    return json_encode($data);
                },
            ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calcul::class,
            'choices' => []
        ]);
    }
}
