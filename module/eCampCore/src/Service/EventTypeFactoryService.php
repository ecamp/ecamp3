<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\EventTypeFactoryHydrator;
use eCamp\Core\Entity\EventTypeFactory;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class EventTypeFactoryService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , EventTypeFactoryHydrator $eventTypeFactoryHydrator
    ) {
        parent::__construct
        ( $acl
        , $entityManager
        , $eventTypeFactoryHydrator
        , EventTypeFactory::class
        );
    }
}
