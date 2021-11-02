<?php

namespace App\Repository;

use App\Entity\ContentNode\SingleText;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|SingleText find($id, $lockMode = null, $lockVersion = null)
 * @method null|SingleText findOneBy(array $criteria, array $orderBy = null)
 * @method SingleText[]    findAll()
 * @method SingleText[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SingleTextRepository extends ContentNodeRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, SingleText::class);
    }
}
