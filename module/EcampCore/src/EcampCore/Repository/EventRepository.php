<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

class EventRepository extends EntityRepository
{

    public function findByCamp($campId)
    {
        return $this->findBy(array('camp' => $campId));
    }

}
