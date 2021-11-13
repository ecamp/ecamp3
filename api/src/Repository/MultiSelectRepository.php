<?php

namespace App\Repository;

use App\Entity\ContentNode\MultiSelect;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|MultiSelect find($id, $lockMode = null, $lockVersion = null)
 * @method null|MultiSelect findOneBy(array $criteria, array $orderBy = null)
 * @method MultiSelect[]    findAll()
 * @method MultiSelect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MultiSelectRepository extends ContentNodeRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, MultiSelect::class);
    }
}
