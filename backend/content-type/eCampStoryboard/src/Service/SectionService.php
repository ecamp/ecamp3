<?php

namespace eCamp\ContentType\Storyboard\Service;

use eCamp\ContentType\Storyboard\Entity\Section;
use eCamp\ContentType\Storyboard\Entity\SectionCollection;
use eCamp\ContentType\Storyboard\Hydrator\SectionHydrator;
use eCamp\Core\ContentType\BaseContentTypeService;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class SectionService extends BaseContentTypeService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            Section::class,
            SectionCollection::class,
            SectionHydrator::class,
            $authenticationService
        );
    }
}
