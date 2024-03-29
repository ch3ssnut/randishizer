<?php

namespace App\Service;

use App\Entity\Dish;
use Doctrine\ORM\EntityManagerInterface;

class MealRandomizer {
    // This service is getting Dish repository and takes meal name passed as an arg. 
    
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function MealRandomizer(string $mealName): array {
        $mealArr = $this->entityManager->getRepository(Dish::class)
        ->findBy([
            'type' => $mealName,
        ]);
        shuffle($mealArr);
        


        return $mealArr;
    }

}