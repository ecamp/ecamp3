<?php

namespace App\Repository;

use App\Entity\ContentNode\ChecklistNode;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method null|ChecklistNode find($id, $lockMode = null, $lockVersion = null)
 * @method null|ChecklistNode findOneBy(array $criteria, array $orderBy = null)
 * @method ChecklistNode[]    findAll()
 * @method ChecklistNode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @template-extends ContentNodeRepository<ChecklistNode>
 */
class ChecklistNodeRepository extends ContentNodeRepository {
    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em, ChecklistNode::class);
    }
}
