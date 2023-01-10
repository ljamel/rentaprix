<?php

namespace App\Form;

use App\Entity\FixedFeeCalcul;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback as ConstraintsCallback;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class FixedFeeCalculType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
                'constraints' => [
                    new Positive([
                        'message' => 'Quantité invalide'
                    ]),
                ],
            ])
            ->add('fixedFee', FixedFeeType::class
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FixedFeeCalcul::class,

        ]);
    }
}
