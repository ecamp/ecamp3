<?php

namespace App\Repository;

use App\Entity\ContentNode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|ContentNode find($id, $lockMode = null, $lockVersion = null)
 * @method null|ContentNode findOneBy(array $criteria, array $orderBy = null)
 * @method ContentNode[]    findAll()
 * @method ContentNode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentNodeRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ContentNode::class);
    }

    // /**
    //  * @return ContentNode[] Returns an array of ContentNode objects
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
    public function findOneBySomeField($value): ?ContentNode
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
