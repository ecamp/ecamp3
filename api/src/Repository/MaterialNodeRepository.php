<?php

namespace App\Repository;

use App\Entity\ContentNode\MaterialNode;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method null|MaterialNode find($id, $lockMode = null, $lockVersion = null)
 * @method null|MaterialNode findOneBy(array $criteria, array $orderBy = null)
 * @method MaterialNode[]    findAll()
 * @method MaterialNode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @template-extends ContentNodeRepository<MaterialNode>
 */
class MaterialNodeRepository extends ContentNodeRepository {
    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em, MaterialNode::class);
    }
}
