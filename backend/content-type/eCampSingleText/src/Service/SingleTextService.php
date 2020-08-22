<?php

namespace eCamp\ContentType\SingleText\Service;

use eCamp\ContentType\SingleText\Entity\SingleText;
use eCamp\ContentType\SingleText\Hydrator\SingleTextHydrator;
use eCamp\Core\ContentType\BaseContentTypeService;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class SingleTextService extends BaseContentTypeService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            SingleText::class,
            SingleTextHydrator::class,
            $authenticationService
        );
    }
}
