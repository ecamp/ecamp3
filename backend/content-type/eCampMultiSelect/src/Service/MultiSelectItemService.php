<?php

namespace eCamp\ContentType\MultiSelect\Service;

use eCamp\ContentType\MultiSelect\Entity\MultiSelectItem;
use eCamp\Core\ContentType\BaseContentTypeService;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class MultiSelectItemService extends BaseContentTypeService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            MultiSelectItem::class,
            MultiSelectItemService::class,
            $authenticationService
        );
    }
}
