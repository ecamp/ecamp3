<?php

namespace App\Repository;

use App\Entity\ContentNode\DefaultLayout;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method null|DefaultLayout find($id, $lockMode = null, $lockVersion = null)
 * @method null|DefaultLayout findOneBy(array $criteria, array $orderBy = null)
 * @method DefaultLayout[]    findAll()
 * @method DefaultLayout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @template-extends ContentNodeRepository<DefaultLayout>
 */
class DefaultLayoutRepository extends ContentNodeRepository {
    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em, DefaultLayout::class);
    }
}
