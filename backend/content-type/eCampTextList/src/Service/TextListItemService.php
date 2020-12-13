<?php

namespace eCamp\ContentType\TextList\Service;

use eCamp\ContentType\SingleText\Entity\TextListItem;
use eCamp\Core\ContentType\BaseContentTypeService;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class TextListItemService extends BaseContentTypeService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            TextListItem::class,
            TextListItemService::class,
            $authenticationService
        );
    }
}
