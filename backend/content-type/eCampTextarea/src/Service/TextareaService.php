<?php

namespace eCamp\ContentType\Textarea\Service;

use eCamp\ContentType\Textarea\Entity\Textarea;
use eCamp\ContentType\Textarea\Hydrator\TextareaHydrator;
use eCamp\Core\ContentType\BaseContentTypeService;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class TextareaService extends BaseContentTypeService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            Textarea::class,
            TextareaHydrator::class,
            $authenticationService
        );
    }
}
