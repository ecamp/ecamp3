<?php

namespace eCamp\Plugin\Textarea\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Plugin\BasePluginService;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Plugin\Textarea\Entity\Textarea;
use eCamp\Plugin\Textarea\Hydrator\TextareaHydrator;
use ZF\ApiProblem\ApiProblem;

class TextareaService extends BasePluginService {
    public function __construct(
        Acl $acl,
        EntityManager $entityManager,
        TextareaHydrator $textareaHydrator,
        $eventPluginId
    ) {
        parent::__construct($acl, $entityManager, $textareaHydrator, Textarea::class, $eventPluginId);
    }
}
