<?php

namespace App\Repository;

use App\Entity\SingleText;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|SingleText find($id, $lockMode = null, $lockVersion = null)
 * @method null|SingleText findOneBy(array $criteria, array $orderBy = null)
 * @method SingleText[]    findAll()
 * @method SingleText[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SingleTextRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, SingleText::class);
    }

    // /**
    //  * @return SingleText[] Returns an array of SingleText objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SingleText
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
