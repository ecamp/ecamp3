<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\EventTypePluginHydrator;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class EventTypePluginService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , EventTypePluginHydrator $eventTypePluginHydrator
    ) {
        parent::__construct
        ( $acl
        , $entityManager
        , $eventTypePluginHydrator
        , EventTypePlugin::class
        );
    }
}
