<?php

namespace App\Repository;

use App\Entity\ContentNode\MultiSelect;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method null|MultiSelect find($id, $lockMode = null, $lockVersion = null)
 * @method null|MultiSelect findOneBy(array $criteria, array $orderBy = null)
 * @method MultiSelect[]    findAll()
 * @method MultiSelect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @template-extends ContentNodeRepository<MultiSelect>
 */
class MultiSelectRepository extends ContentNodeRepository {
    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em, MultiSelect::class);
    }
}
