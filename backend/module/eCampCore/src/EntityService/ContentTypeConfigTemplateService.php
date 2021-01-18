<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\ContentType;
use eCamp\Core\Entity\ContentTypeConfigTemplate;
use eCamp\Core\Hydrator\ContentTypeConfigTemplateHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class ContentTypeConfigTemplateService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            ContentTypeConfigTemplate::class,
            ContentTypeConfigTemplateHydrator::class,
            $authenticationService
        );
    }

    protected function createEntity($data) {
        /** @var ContentTypeConfigTemplate $contentTypeConfigTemplate */
        $contentTypeConfigTemplate = parent::createEntity($data);

        /** @var ActivityCategoryTemplate $activityCategoryTemplate */
        $activityCategoryTemplate = $this->findRelatedEntity(ActivityCategoryTemplate::class, $data, 'activityCategoryTemplateId');
        $activityCategoryTemplate->addContentTypeConfig($contentTypeConfigTemplate);

        /** @var ContentType $contentType */
        $contentType = $this->findRelatedEntity(ContentType::class, $data, 'contentTypeId');
        $contentTypeConfigTemplate->setContentType($contentType);

        return $contentTypeConfigTemplate;
    }
}
