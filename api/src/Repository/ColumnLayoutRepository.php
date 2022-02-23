<?php

namespace App\Repository;

use App\Entity\ContentNode\ColumnLayout;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method null|ColumnLayout find($id, $lockMode = null, $lockVersion = null)
 * @method null|ColumnLayout findOneBy(array $criteria, array $orderBy = null)
 * @method ColumnLayout[]    findAll()
 * @method ColumnLayout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ColumnLayoutRepository extends ContentNodeRepository {
    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em, ColumnLayout::class);
    }
}
