<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ContentNode\NoData;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method null|NoData find($id, $lockMode = null, $lockVersion = null)
 * @method null|NoData findOneBy(array $criteria, array $orderBy = null)
 * @method NoData[]    findAll()
 * @method NoData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @template-extends ContentNodeRepository<NoData>
 */
class NoDataRepository extends ContentNodeRepository {
    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em, NoData::class);
    }
}
