<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\ContentType\ContentTypeStrategyProvider;
use eCamp\Core\ContentType\ContentTypeStrategyProviderTrait;
use eCamp\Core\Entity\AbstractContentNodeOwner;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Hydrator\ContentNodeHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ContentNodeService extends AbstractEntityService {
    use ContentTypeStrategyProviderTrait;

    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService, ContentTypeStrategyProvider $contentTypeStrategyProvider) {
        parent::__construct(
            $serviceUtils,
            ContentNode::class,
            ContentNodeHydrator::class,
            $authenticationService
        );

        $this->setContentTypeStrategyProvider($contentTypeStrategyProvider);
    }

    public function createFromPrototype(AbstractContentNodeOwner $owner, ContentNode $prototype): ContentNode {
        /** @var ContentNode $contentNode */
        $contentNode = $this->create((object) [
            'ownerId' => $owner->getId(),
            'contentTypeId' => $prototype->getContentType()->getId(),
            'instanceName' => $prototype->getInstanceName(),
            'slot' => $prototype->getSlot(),
            'position' => $prototype->getPosition(),
            'config' => $prototype->getConfig(),
        ]);

        foreach ($prototype->getChildren() as $childPrototype) {
            $childContentNode = $this->createFromPrototype($owner, $childPrototype);
            $contentNode->addChild($childContentNode);
        }

        return $contentNode;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws NoAccessException
     */
    protected function createEntity($data): ContentNode {
        /** @var ContentNode $contentNode */
        $contentNode = parent::createEntity($data);

        /** @var AbstractContentNodeOwner $owner */
        $owner = $this->findRelatedEntity(AbstractContentNodeOwner::class, $data, 'ownerId');

        /** @var ContentType $contentType */
        $contentType = $this->findRelatedEntity(ContentType::class, $data, 'contentTypeId');

        $owner->addContentNode($contentNode);
        $contentNode->setContentType($contentType);
        $contentNode->setContentTypeStrategyProvider($this->getContentTypeStrategyProvider());

        return $contentNode;
    }

    protected function patchEntity(BaseEntity $entity, $data): ContentNode {
        /** @var ContentNode $contentNode */
        $contentNode = parent::patchEntity($entity, $data);

        if (isset($data->parentId)) {
            /** @var ContentNode $parent */
            $parent = $this->findRelatedEntity(ContentNode::class, $data, 'parentId');
            $parent->addChild($contentNode);
        }

        return $entity;
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
        $q = parent::fetchAllQueryBuilder($params);
        $q->join('row.activity', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

        if (isset($params['activityId'])) {
            $q->andWhere('row.activity = :activityId');
            $q->setParameter('activityId', $params['activityId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id): QueryBuilder {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.activity', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

        return $q;
    }
}
