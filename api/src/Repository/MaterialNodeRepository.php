<?php

namespace App\Repository;

use App\Entity\ContentNode\MaterialNode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|MaterialNode find($id, $lockMode = null, $lockVersion = null)
 * @method null|MaterialNode findOneBy(array $criteria, array $orderBy = null)
 * @method MaterialNode[]    findAll()
 * @method MaterialNode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialNodeRepository extends ContentNodeRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, MaterialNode::class);
    }
}
