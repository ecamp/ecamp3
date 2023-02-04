<?php

namespace App\Repository;

use App\Entity\ContentNode\SingleText;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method null|SingleText find($id, $lockMode = null, $lockVersion = null)
 * @method null|SingleText findOneBy(array $criteria, array $orderBy = null)
 * @method SingleText[]    findAll()
 * @method SingleText[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @template-extends ContentNodeRepository<SingleText>
 */
class SingleTextRepository extends ContentNodeRepository {
    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em, SingleText::class);
    }
}
