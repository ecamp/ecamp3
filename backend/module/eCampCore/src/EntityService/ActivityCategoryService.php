<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\ActivityType;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Hydrator\ActivityCategoryHydrator;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;
use ZF\ApiProblem\ApiProblem;

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
     * @throws ORMException
     * @throws NoAccessException
     *
     * @return ActivityCategory|ApiProblem
     */
    public function create($data) {
        /** @var ActivityCategory $activityCategory */
        $activityCategory = parent::create($data);

        /** @var ActivityType $activityType */
        $activityType = $this->findEntity(ActivityType::class, $data->activityTypeId);
        $activityCategory->setActivityType($activityType);

        /** @var Camp $camp */
        $camp = $this->findEntity(Camp::class, $data->campId);
        $camp->addActivityCategory($activityCategory);

        $this->assertAllowed($activityCategory, Acl::REST_PRIVILEGE_CREATE);

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
