<?php

namespace App\Form;

use App\Entity\Calcul;
use App\Entity\VariableFee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CalculType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('date')
            ->add('devis')
            ->add('durationMonth')
            ->add(
                'variableFees',
                CollectionType::class,
                [
                  'entry_type' => VariableFeeType::class, // le formulaire enfant qui doit être répété
                  'allow_add' => true, // true si tu veux que l'utilisateur puisse en ajouter
                  'allow_delete' => false, // true si tu veux que l'utilisateur puisse en supprimer
                  'label' => 'Frais variables',
                  'by_reference' => true, // voir  https://symfony.com/doc/current/reference/forms/types/collection.html#by-reference
               
                ]
              )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calcul::class,
        ]);
    }
}
