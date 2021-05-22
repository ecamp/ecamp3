<?php

namespace App\Repository;

use App\Entity\Camp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Camp find($id, $lockMode = null, $lockVersion = null)
 * @method null|Camp findOneBy(array $criteria, array $orderBy = null)
 * @method Camp[]    findAll()
 * @method Camp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Camp::class);
    }

    // /**
    //  * @return Camp[] Returns an array of Camp objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Camp
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
