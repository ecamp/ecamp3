<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

class GroupRepository extends EntityRepository
{

    public function findRootGroups()
    {
        return $this->createQueryBuilder("g")
                ->where("g.parent IS NULL ")
                ->getQuery()->getResult();
    }

}
