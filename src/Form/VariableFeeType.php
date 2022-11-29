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
                'label'=> 'Titre'
            ])
            ->add('price', MoneyType::class, [
            'label'=> 'Prix',
            'currency' => ''
            ])
            ->add('unit', TextType::class, [
                'label'=> 'Unité'
            ])
            ->add('type', TextType::class, [
                'label' => 'Type'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VariableFee::class,
        ]);
    }
}
