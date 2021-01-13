<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Entity\ContentTypeConfig;
use eCamp\Core\Hydrator\ContentTypeConfigHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class ContentTypeConfigService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            ContentTypeConfig::class,
            ContentTypeConfigHydrator::class,
            $authenticationService
        );
    }

    protected function createEntity($data) {
        /** @var ContentTypeConfig $contentTypeConfig */
        $contentTypeConfig = parent::createEntity($data);

        /** @var ActivityCategory $activityCategory */
        $activityCategory = $this->findRelatedEntity(ActivityCategory::class, $data, 'activityCategoryId');
        $activityCategory->addContentTypeConfig($contentTypeConfig);

        /** @var ContentType $contentType */
        $contentType = $this->findRelatedEntity(ContentType::class, $data, 'contentTypeId');
        $contentTypeConfig->setContentType($contentType);

        return $contentTypeConfig;
    }
}
