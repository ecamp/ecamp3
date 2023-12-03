<?php

namespace App\Repository;

use App\Entity\ContentNode\ResponsiveLayout;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method null|ResponsiveLayout find($id, $lockMode = null, $lockVersion = null)
 * @method null|ResponsiveLayout findOneBy(array $criteria, array $orderBy = null)
 * @method ResponsiveLayout[]    findAll()
 * @method ResponsiveLayout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @template-extends ContentNodeRepository<ResponsiveLayout>
 */
class ResponsiveLayoutRepository extends ContentNodeRepository {
    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em, ResponsiveLayout::class);
    }
}
