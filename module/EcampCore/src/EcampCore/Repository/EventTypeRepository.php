<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;
use EcampCore\Entity\EventType;

/**
 * Class EventTypeRepository
 * @package EcampCore\Repository
 *
 * @method EventType find($id)
 */
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
