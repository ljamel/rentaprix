<?php

namespace App\Form;

use App\Entity\Salary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('FullName', TextType::class, [
                'label' => 'Nom et Prénom'
            ])
            ->add('post', TextType::class, [
                'label' => 'Poste occupé'
            ])
            ->add('pay', MoneyType::class, [
                'label' => 'Rémunération'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Salary::class,
        ]);
    }
}
