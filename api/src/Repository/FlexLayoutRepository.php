<?php

namespace App\Repository;

use App\Entity\ContentNode\FlexLayout;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method null|FlexLayout find($id, $lockMode = null, $lockVersion = null)
 * @method null|FlexLayout findOneBy(array $criteria, array $orderBy = null)
 * @method FlexLayout[]    findAll()
 * @method FlexLayout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @template-extends ContentNodeRepository<FlexLayout>
 */
class FlexLayoutRepository extends ContentNodeRepository {
    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em, FlexLayout::class);
    }
}
