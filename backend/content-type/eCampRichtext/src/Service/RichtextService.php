<?php

namespace eCamp\ContentType\Richtext\Service;

use eCamp\ContentType\Richtext\Entity\Richtext;
use eCamp\ContentType\Richtext\Hydrator\RichtextHydrator;
use eCamp\Core\ContentType\BaseContentTypeService;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class RichtextService extends BaseContentTypeService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            Richtext::class,
            RichtextHydrator::class,
            $authenticationService
        );
    }
}
