<?php

namespace eCamp\ContentType\MultiSelect\Service;

use eCamp\ContentType\MultiSelect\Entity\Option;
use eCamp\ContentType\MultiSelect\Hydrator\OptionHydrator;
use eCamp\Core\ContentType\BaseContentTypeService;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class OptionService extends BaseContentTypeService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            Option::class,
            OptionHydrator::class,
            $authenticationService
        );
    }
}
