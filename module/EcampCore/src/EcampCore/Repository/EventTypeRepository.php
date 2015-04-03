<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

class EventTypeRepository
    extends EntityRepository
{

    public function findByCampTypeId($campTypeId)
    {
        $q = $this->createQueryBuilder('et');
        $q->join('et.campTypes', 'ct');
        $q->where('ct.id = :campTypeId');
        $q->setParameter('campTypeId', $campTypeId);

        return $q->getQuery()->getResult();
    }

}
