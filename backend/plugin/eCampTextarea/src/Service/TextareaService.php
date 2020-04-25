<?php

namespace eCamp\Plugin\Textarea\Service;

use eCamp\Core\Plugin\BasePluginService;
use eCamp\Plugin\Textarea\Entity\Textarea;
use eCamp\Plugin\Textarea\Hydrator\TextareaHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class TextareaService extends BasePluginService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            Textarea::class,
            TextareaHydrator::class,
            $authenticationService
        );
    }
}
