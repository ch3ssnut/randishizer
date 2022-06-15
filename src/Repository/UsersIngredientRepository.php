<?php

namespace App\Repository;

use App\Entity\UsersIngredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UsersIngredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsersIngredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsersIngredient[]    findAll()
 * @method UsersIngredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersIngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsersIngredient::class);
    }

    // /**
    //  * @return UsersIngredient[] Returns an array of UsersIngredient objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UsersIngredient
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
