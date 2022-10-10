<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\UsersIngredient;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class IngredientType extends AbstractType
{   
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


        $builder
            ->add('name', EntityType::class, [
                'class' => UsersIngredient::class,
                'query_builder' => function (EntityRepository $er) {
                    $query = $er->createQueryBuilder('p')
                        ->where('p.Owner = :owner')
                        ->setParameter('owner', $this->security->getUser())
                        ->orderBy('p.Ingredient', 'ASC');
                    // dd($query);
                    return $query;
                },
                'choice_label' => function ($usersIngredient) {
                        $name = $usersIngredient->getIngredient() . ' (' . $usersIngredient->getUnit() . ')';
                        return $name;
                    },

            ])
            
            ->add('ammount', NumberType::class, [
                'required' => false,
            ])
            ->add('Submit', SubmitType::class)
            ->add('Remove', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}
