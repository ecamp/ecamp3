<?php

namespace App\Repository;

use App\Entity\ContentNode\Storyboard;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method null|Storyboard find($id, $lockMode = null, $lockVersion = null)
 * @method null|Storyboard findOneBy(array $criteria, array $orderBy = null)
 * @method Storyboard[]    findAll()
 * @method Storyboard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoryboardRepository extends ContentNodeRepository {
    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em, Storyboard::class);
    }
}
