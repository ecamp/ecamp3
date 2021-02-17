<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\CategoryContent;
use eCamp\Core\Entity\CategoryContentTemplate;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Hydrator\CategoryContentHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class CategoryContentService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            CategoryContent::class,
            CategoryContentHydrator::class,
            $authenticationService
        );
    }

    /**
     * Create CategoryContent and all Child-CategoryContents.
     */
    public function createFromTemplate(Category $category, CategoryContentTemplate $template): CategoryContent {
        /** @var CategoryContent $categoryContent */
        $categoryContent = $this->create((object) [
            'categoryId' => $category->getId(),
            'contentTypeId' => $template->getContentType()->getId(),
            'instanceName' => $template->getInstanceName(),
            'position' => $template->getPosition(),
        ]);
        $categoryContent->setCategoryContentTemplateId($template->getId());

        foreach ($template->getChildren() as $childTemplate) {
            $childCategoryContent = $this->createFromTemplate($category, $childTemplate);
            $categoryContent->addChild($childCategoryContent);
        }

        return $categoryContent;
    }

    protected function createEntity($data): CategoryContent {
        /** @var CategoryContent $categoryContent */
        $categoryContent = parent::createEntity($data);

        /** @var Category $category */
        $category = $this->findRelatedEntity(Category::class, $data, 'categoryId');
        $category->addCategoryContent($categoryContent);

        /** @var ContentType $contentType */
        $contentType = $this->findRelatedEntity(ContentType::class, $data, 'contentTypeId');
        $categoryContent->setContentType($contentType);

        return $categoryContent;
    }
}
