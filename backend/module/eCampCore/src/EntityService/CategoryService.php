<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\CategoryContentTemplate;
use eCamp\Core\Entity\CategoryContentTypeTemplate;
use eCamp\Core\Entity\CategoryTemplate;
use eCamp\Core\Hydrator\CategoryHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class CategoryService extends AbstractEntityService {
    protected CategoryContentTypeService $categoryContentTypeService;
    protected CategoryContentService $categoryContentService;

    public function __construct(
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        CategoryContentTypeService $categoryContentTypeService,
        CategoryContentService $categoryContentService
    ) {
        parent::__construct(
            $serviceUtils,
            Category::class,
            CategoryHydrator::class,
            $authenticationService
        );

        $this->categoryContentTypeService = $categoryContentTypeService;
        $this->categoryContentService = $categoryContentService;
    }

    public function createFromTemplate(Camp $camp, CategoryTemplate $template): Category {
        /** @var Category $category */
        $category = $this->create((object) [
            'campId' => $camp->getId(),
            'short' => $template->getShort(),
            'name' => $template->getName(),
            'color' => $template->getColor(),
            'numberingStyle' => $template->getNumberingStyle(),
        ]);
        $category->setCategoryTemplateId($template->getId());

        /** @var CategoryContentTypeTemplate $categoryContentTypeTemplate */
        foreach ($template->getCategoryContentTypeTemplates() as $categoryContentTypeTemplate) {
            $this->categoryContentTypeService->createFromTemplate($category, $categoryContentTypeTemplate);
        }

        /** @var CategoryContentTemplate $categoryContentTemplate */
        foreach ($template->getRootCategoryContentTemplates() as $categoryContentTemplate) {
            $this->categoryContentService->createFromTemplate($category, $categoryContentTemplate);
        }

        return $category;
    }

    /**
     * @param mixed $data
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws NoAccessException
     */
    protected function createEntity($data): Category {
        /** @var Category $category */
        $category = parent::createEntity($data);

        /** @var Camp $camp */
        $camp = $this->findRelatedEntity(Camp::class, $data, 'campId');
        $camp->addCategory($category);

        return $category;
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
        $q = parent::fetchAllQueryBuilder($params);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        if (isset($params['campId'])) {
            $q->andWhere('row.camp = :campId');
            $q->setParameter('campId', $params['campId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id): QueryBuilder {
        $q = parent::fetchQueryBuilder($id);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        return $q;
    }
}
