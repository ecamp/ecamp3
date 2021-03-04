<?php

namespace eCamp\Core\ContentType;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\EntityService\AbstractEntityService;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;
use Laminas\Paginator\Paginator;

abstract class BaseContentTypeService extends AbstractEntityService {
    public function __construct(
        ServiceUtils $serviceUtils,
        string $entityClassname,
        string $hydratorClassname,
        AuthenticationService $authenticationService
    ) {
        parent::__construct($serviceUtils, $entityClassname, $hydratorClassname, $authenticationService);
    }

    /**
     * Returns a single contentType entity attached to $contentNodeId (1:1 connection).
     */
    public function findOneByContentNode($contentNodeId): BaseEntity {
        return $this->getRepository()->findOneBy(['contentNode' => $contentNodeId]);
    }

    /**
     * Returns all contentType entities attached to $contentNodeId (1:n connection).
     *
     * @param string $contentNodeId
     *
     * @throws NoAccessException
     */
    public function fetchAllByContentNode($contentNodeId): Paginator {
        return $this->fetchAll(['contentNodeId' => $contentNodeId]);
    }

    /**
     * @throws NoAccessException
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    public function createEntity($data, ?ContentNode $contentNode = null): BaseContentTypeEntity {
        /** @var BaseContentTypeEntity $entity */
        $entity = parent::createEntity($data);

        if (isset($data->contentNodeId)) {
            /** @var ContentNode $contentNode */
            $contentNode = $this->findEntity(ContentNode::class, $data->contentNodeId);
            $entity->setContentNode($contentNode);
        } elseif ($contentNode) {
            $entity->setContentNode($contentNode);
        }

        return $entity;
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
        $q = parent::fetchAllQueryBuilder($params);

        if (is_subclass_of($this->entityClass, BaseContentTypeEntity::class)) {
            $q->join('row.contentNode', 'cn');

            if (isset($params['contentNodeId'])) {
                $q->andWhere('row.contentNode = :contentNodeId');
                $q->setParameter('contentNodeId', $params['contentNodeId']);
            }
        }

        return $q;
    }
}
