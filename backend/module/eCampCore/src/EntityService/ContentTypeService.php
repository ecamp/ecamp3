<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\ContentType;
use eCamp\Core\Hydrator\ContentTypeHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class ContentTypeService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            ContentType::class,
            ContentTypeHydrator::class,
            $authenticationService
        );
    }
}
