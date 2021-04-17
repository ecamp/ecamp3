<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\CategoryContentType;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Hydrator\CategoryContentTypeHydrator;
use eCamp\Lib\Service\ServiceUtils;
use eCampApi\V1\Rest\CategoryContentType\CategoryContentTypeCollection;
use Laminas\Authentication\AuthenticationService;

class CategoryContentTypeService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            CategoryContentType::class,
            CategoryContentTypeCollection::class,
            CategoryContentTypeHydrator::class,
            $authenticationService
        );
    }

    public function createFromPrototype(Category $category, CategoryContentType $prototype) {
        /** @var CategoryContentType $categoryContentType */
        $categoryContentType = $this->create((object) [
            'categoryId' => $category->getId(),
            'contentTypeId' => $prototype->getContentType()->getId(),
        ]);
        $categoryContentType->setCategoryContentTypePrototypeId($prototype->getId());

        return $categoryContentType;
    }

    protected function createEntity($data): CategoryContentType {
        /** @var CategoryContentType $categoryContentType */
        $categoryContentType = parent::createEntity($data);

        /** @var Category $category */
        $category = $this->findRelatedEntity(Category::class, $data, 'categoryId');
        $category->addCategoryContentType($categoryContentType);

        /** @var ContentType $contentType */
        $contentType = $this->findRelatedEntity(ContentType::class, $data, 'contentTypeId');
        $categoryContentType->setContentType($contentType);

        return $categoryContentType;
    }
}
