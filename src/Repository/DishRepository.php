<?php

namespace App\Repository;

use App\Entity\Dish;
use App\Entity\Ingredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method Dish|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dish|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dish[]    findAll()
 * @method Dish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishRepository extends ServiceEntityRepository
{
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Dish::class);
        $this->security = $security;
    }

    /**
    * @return Dish[] Returns an array of Dish objects
    */
    
    public function findMealsByDish($meal)
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.ingredients', 'ingredients')
            ->addSelect('ingredients')
            ->where('d.type = :meal')
            ->setParameter('meal', $meal)
            ->andWhere('d.owner = :owner')
            ->setParameter('owner', $this->security->getUser())
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    

    /*
    public function findOneBySomeField($value): ?Dish
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
