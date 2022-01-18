<?php

namespace App\Controller\Admin;

use App\Entity\Dish;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DishCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Dish::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextField::new('type'),
            AssociationField::new('owner'),
            AssociationField::new('ingredients')
        ];
    }
    
}
