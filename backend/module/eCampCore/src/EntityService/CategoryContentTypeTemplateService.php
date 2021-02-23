<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\CategoryContentTypeTemplate;
use eCamp\Core\Entity\CategoryTemplate;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Hydrator\CategoryContentTypeTemplateHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class CategoryContentTypeTemplateService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            CategoryContentTypeTemplate::class,
            CategoryContentTypeTemplateHydrator::class,
            $authenticationService
        );
    }

    protected function createEntity($data): CategoryContentTypeTemplate {
        /** @var CategoryContentTypeTemplate $categoryContentTypeTemplate */
        $categoryContentTypeTemplate = parent::createEntity($data);

        /** @var CategoryTemplate $categoryTemplate */
        $categoryTemplate = $this->findRelatedEntity(CategoryTemplate::class, $data, 'categoryTemplateId');
        $categoryTemplate->addCategoryContentTypeTemplate($categoryContentTypeTemplate);

        /** @var ContentType $contentType */
        $contentType = $this->findRelatedEntity(ContentType::class, $data, 'contentTypeId');
        $categoryContentTypeTemplate->setContentType($contentType);

        return $categoryContentTypeTemplate;
    }
}
