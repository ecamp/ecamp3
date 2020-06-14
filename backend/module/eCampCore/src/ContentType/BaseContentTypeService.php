<?php

namespace eCamp\Core\ContentType;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Core\EntityService\AbstractEntityService;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\ApiTools\ApiProblem\ApiProblem;
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
     * @param mixed $activityContentid
     * @param mixed $activityContentId
     *
     * @return BaseEntity
     */
    public function findOneByActivityContent($activityContentId) {
        return $this->getRepository()->findOneBy(['activityContent' => $activityContentId]);
    }

    /**
     * Returns all contentType entities attached to $activityContentId (1:n connection).
     *
     * @param string $activityContentId
     *
     * @return Paginator
     */
    public function fetchAllByActivityContent($activityContentId) {
        return $this->fetchAll(['activityContentId' => $activityContentId]);
    }

    /**
     * @param mixed $data
     *
     * @throws NoAccessException
     * @throws ORMException
     *
     * @return ApiProblem|BaseContentTypeEntity
     */
    public function createEntity($data, ?ActivityContent $activityContent = null) {
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

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);

        if (isset($params['activityContentId'])) {
            $q->andWhere('row.activityContent = :activitycontentTypeId');
            $q->setParameter('activitycontentTypeId', $params['activityContentId']);
        }

        return $q;
    }
}
