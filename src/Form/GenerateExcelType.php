<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenerateExcelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Number_of_days', IntegerType::class,
            [
                'attr' => [
                    'min'=>0,
                    'max'=>30,
                ]
            ])
            ->add('Number_of_shopping_days', IntegerType::class,
            [
                'required'=> false,
                'data'=> 1,
                'attr' => [
                    'min'=>1,
                    'max'=>30,
                ]
            ])
            ->add('Submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
