<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\ContentType;
use eCamp\Core\Hydrator\ContentTypeHydrator;
use eCamp\Lib\Service\ServiceUtils;
use eCampApi\V1\Rest\ContentType\ContentTypeCollection;
use Laminas\Authentication\AuthenticationService;

class ContentTypeService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            ContentType::class,
            ContentTypeCollection::class,
            ContentTypeHydrator::class,
            $authenticationService
        );
    }
}
