<?php

namespace eCamp\Plugin\Textarea\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Plugin\BasePluginService;
use eCamp\Lib\Acl\Acl;
use eCamp\Plugin\Textarea\Entity\Textarea;
use eCamp\Plugin\Textarea\Hydrator\TextareaHydrator;

class TextareaService extends BasePluginService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , TextareaHydrator $textareaHydrator
    , $eventPluginId
    ) {
        parent::__construct
        ( $acl
        , $entityManager
        , $textareaHydrator
        , Textarea::class
        , $eventPluginId
        );
    }
}
