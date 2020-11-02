<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Hydrator\ActivityCategoryHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\EntityValidationException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class ActivityCategoryService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            ActivityCategory::class,
            ActivityCategoryHydrator::class,
            $authenticationService
        );
    }

    /**
     * @param mixed $data
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws NoAccessException
     *
     * @return ActivityCategory
     */
    protected function createEntity($data) {
        /** @var ActivityCategory $activityCategory */
        $activityCategory = parent::createEntity($data);

        /** @var ActivityType $activityType */
        $activityType = $this->findRelatedEntity(ActivityType::class, $data, 'activityTypeId');
        $activityCategory->setActivityType($activityType);

        /** @var Camp $camp */
        $camp = $this->findRelatedEntity(Camp::class, $data, 'campId');
        $camp->addActivityCategory($activityCategory);

        if (!$camp->getCampType()->getActivityTypes()->contains($activityType)) {
            throw (new EntityValidationException())->setMessages([
                'campId' => ['activityTypeMismatch' => 'Selected activityType is not allowed in this camp'],
                'activityTypeId' => ['activityTypeMismatch' => 'Selected activityType is not allowed in this camp'],
            ]);
        }

        return $activityCategory;
    }

    /**
     * @param $data
     *
     * @throws EntityNotFoundException
     * @throws NoAccessException
     * @throws NonUniqueResultException
     *
     * @return ActivityCategory
     */
    protected function patchEntity(BaseEntity $entity, $data) {
        /** @var ActivityCategory $activityCategory */
        $activityCategory = parent::patchEntity($entity, $data);

        if (isset($data->activityTypeId)) {
            /** @var ActivityType $activityType */
            $activityType = $this->findEntity(ActivityType::class, $data->activityTypeId);
            $activityCategory->setActivityType($activityType);
        }

        return $activityCategory;
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        if (isset($params['campId'])) {
            $q->andWhere('row.camp = :campId');
            $q->setParameter('campId', $params['campId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        return $q;
    }
}
