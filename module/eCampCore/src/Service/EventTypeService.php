<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\EventTypeHydrator;
use eCamp\Core\Entity\EventType;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class EventTypeService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , EventTypeHydrator $eventTypeHydrator
    ) {
        parent::__construct
        ( $acl
        , $entityManager
        , $eventTypeHydrator
        , EventType::class
        );
    }
}
