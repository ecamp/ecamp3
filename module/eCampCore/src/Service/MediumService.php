<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\MediumHydrator;
use eCamp\Core\Entity\Medium;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class MediumService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , MediumHydrator $mediumHydrator
    ) {
        parent::__construct
        ( $acl
        , $entityManager
        , $mediumHydrator
        , Medium::class
        );
    }
}
