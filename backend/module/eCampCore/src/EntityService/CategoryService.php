<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Hydrator\CategoryHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;
use eCampApi\V1\Rest\Category\CategoryCollection;
use Laminas\Authentication\AuthenticationService;

class CategoryService extends AbstractEntityService {
    private ContentNodeService $contentNodeService;

    public function __construct(
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        ContentNodeService $contentNodeService
    ) {
        parent::__construct(
            $serviceUtils,
            Category::class,
            CategoryCollection::class,
            CategoryHydrator::class,
            $authenticationService
        );

        $this->contentNodeService = $contentNodeService;
    }

    public function createFromPrototype(Camp $camp, Category $prototype): Category {
        /** @var ContentNode $contentNodePrototype */
        $contentNodePrototype = $prototype->getRootContentNode();

        /** @var Category $category */
        $category = $this->create((object) [
            'campId' => $camp->getId(),
            'short' => $prototype->getShort(),
            'name' => $prototype->getName(),
            'color' => $prototype->getColor(),
            'numberingStyle' => $prototype->getNumberingStyle(),
            'createRootContentNode' => !isset($contentNodePrototype),
        ]);
        $category->setCategoryPrototypeId($prototype->getId());

        if (isset($contentNodePrototype)) {
            $contentNode = $this->contentNodeService->createFromPrototype($category, $contentNodePrototype);
            $category->setRootContentNode($contentNode);
        }

        /** @var ContentType $contentType */
        foreach ($prototype->getPreferredContentTypes() as $contentType) {
            $category->addPreferredContentType($contentType);
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

        if (isset($data->preferredContentTypes)) {
            array_map(
                function ($contentType) use ($category) {
                    $category->addPreferredContentType($contentType);
                },
                $this->findRelatedEntities(ContentType::class, $data, 'preferredContentTypes')
            );
        }

        return $category;
    }

    protected function createEntityPost(BaseEntity $entity, $data): BaseEntity {
        /** @var Category $category */
        $category = parent::createEntityPost($entity, $data);
        
        if (!isset($data->createRootContentNode) || $data->createRootContentNode) {
            $q = $this->findCollectionQueryBuilder(ContentType::class, 'c', null)->where("c.name = 'ColumnLayout'");
            $columnLayout = $this->getQuerySingleResult($q);
            
            $contentNode = $this->contentNodeService->create((object) [
                'ownerId' => $entity->getId(),
                'contentTypeId' => $columnLayout->getId(),
            ]);
            $category->setRootContentNode($contentNode);
        }

        return $category;
    }

    protected function patchEntity(BaseEntity $entity, $data): BaseEntity {
        /** @var Category $category */
        $category = parent::patchEntity($entity, $data);

        if (isset($data->preferredContentTypes)) {
            $category->getPreferredContentTypes()->clear();
            array_map(
                function ($contentType) use ($category) {
                    $category->addPreferredContentType($contentType);
                },
                $this->findRelatedEntities(ContentType::class, $data, 'preferredContentTypes')
            );
        }

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
