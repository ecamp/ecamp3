<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\CategoryContentType;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Hydrator\CategoryHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class CategoryService extends AbstractEntityService {
    private ContentNodeService $contentNodeService;
    private CategoryContentTypeService $categoryContentTypeService;

    public function __construct(
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        ContentNodeService $contentNodeService,
        CategoryContentTypeService $categoryContentTypeService
    ) {
        parent::__construct(
            $serviceUtils,
            Category::class,
            CategoryHydrator::class,
            $authenticationService
        );

        $this->contentNodeService = $contentNodeService;
        $this->categoryContentTypeService = $categoryContentTypeService;
    }

    public function createFromPrototype(Camp $camp, Category $prototype): Category {
        /** @var Category $category */
        $category = $this->create((object) [
            'campId' => $camp->getId(),
            'short' => $prototype->getShort(),
            'name' => $prototype->getName(),
            'color' => $prototype->getColor(),
            'numberingStyle' => $prototype->getNumberingStyle(),
        ]);
        $category->setCategoryPrototypeId($prototype->getId());

        /** @var ContentNode $contentNodePrototype */
        $contentNodePrototype = $prototype->getRootContentNode();
        if (isset($contentNodePrototype)) {
            $contentNode = $this->contentNodeService->createFromPrototype($category, $contentNodePrototype);
            $category->setRootContentNode($contentNode);
        }

        /** @var CategoryContentType $categoryContentType */
        foreach ($prototype->getCategoryContentTypes() as $categoryContentType) {
            $this->categoryContentTypeService->createFromPrototype($category, $categoryContentType);
        }

        return $category;
    }

    /**
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
