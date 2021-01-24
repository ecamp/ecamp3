<?php

namespace eCamp\Core\ContentType;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Core\Entity\Camp;
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
     * Returns a single contentType entity attached to $activityContentId (1:1 connection).
     *
     * @param mixed $activityContentId
     */
    public function findOneByActivityContent($activityContentId): BaseEntity {
        return $this->getRepository()->findOneBy(['activityContent' => $activityContentId]);
    }

    /**
     * Returns all contentType entities attached to $activityContentId (1:n connection).
     *
     * @param string $activityContentId
     *
     * @throws NoAccessException
     */
    public function fetchAllByActivityContent($activityContentId): Paginator {
        return $this->fetchAll(['activityContentId' => $activityContentId]);
    }

    /**
     * @param mixed $data
     *
     * @throws NoAccessException
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    public function createEntity($data, ?ActivityContent $activityContent = null): BaseContentTypeEntity {
        /** @var BaseContentTypeEntity $entity */
        $entity = parent::createEntity($data);

        if (isset($data->activityContentId)) {
            /** @var ActivityContent $activityContent */
            $activityContent = $this->findEntity(ActivityContent::class, $data->activityContentId);
            $entity->setActivityContent($activityContent);
        } elseif ($activityContent) {
            $entity->setActivityContent($activityContent);
        }

        return $entity;
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
        $q = parent::fetchAllQueryBuilder($params);

        if (is_subclass_of($this->entityClass, BaseContentTypeEntity::class)) {
            $q->join('row.activityContent', 'ac');
            $q->join('ac.activity', 'a');
            $q->andWhere($this->createFilter($q, Camp::class, 'a', 'camp'));

            if (isset($params['activityContentId'])) {
                $q->andWhere('row.activityContent = :activitycontentTypeId');
                $q->setParameter('activitycontentTypeId', $params['activityContentId']);
            }
        }

        return $q;
    }

    protected function fetchQueryBuilder($id): QueryBuilder {
        $q = parent::fetchQueryBuilder($id);

        if (is_subclass_of($this->entityClass, BaseContentTypeEntity::class)) {
            $q->join('row.activityContent', 'ac');
            $q->join('ac.activity', 'a');
            $q->andWhere($this->createFilter($q, Camp::class, 'a', 'camp'));
        }

        return $q;
    }
}
