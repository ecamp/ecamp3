<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\AbstractCampOwner;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\User;
use eCamp\Core\Hydrator\CampHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\Authentication\AuthenticationService;

class CampService extends AbstractEntityService {
    /** @var PeriodService */
    protected $periodService;

    /** @var ActivityCategoryService */
    protected $activityCategoryService;

    public function __construct(
        ActivityCategoryService $activityCategoryService,
        PeriodService $periodService,
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService
    ) {
        parent::__construct(
            $serviceUtils,
            Camp::class,
            CampHydrator::class,
            $authenticationService
        );

        $this->periodService = $periodService;
        $this->activityCategoryService = $activityCategoryService;
    }

    /**
     * @return ApiProblem|array
     */
    public function fetchByOwner(AbstractCampOwner $owner) {
        $q = parent::findCollectionQueryBuilder(Camp::class, 'row');
        $q->where('row.owner = :owner');
        $q->setParameter('owner', $owner);

        return $this->getQueryResult($q);
    }

    /**
     * @param mixed $data
     *
     * @throws NoAccessException
     * @throws EntityNotFoundException
     * @throws ORMException
     *
     * @return Camp
     */
    protected function createEntity($data) {
        /** @var CampType $campType */
        $campType = $this->findEntity(CampType::class, $data->campTypeId);

        /** @var AbstractCampOwner $owner */
        $owner = $this->findEntity(AbstractCampOwner::class, $data->ownerId);

        /** @var User $creator */
        $creator = $this->getAuthUser();

        /** @var Camp $camp */
        $camp = parent::create($data);
        $camp->setCampType($campType);
        $camp->setCreator($creator);
        $owner->addOwnedCamp($camp);

        return $camp;
    }

    protected function createEntityPost(BaseEntity $entity, $data) {
        /** @var Camp $camp */
        $camp = $entity;
        $campType = $camp->getCampType();

        /** Create default Jobs */
        $jobConfigs = $campType->getConfig(CampType::CNF_JOBS) ?: [];
        foreach ($jobConfigs as $jobConfig) {
            $jobConfig->campId = $camp->getId();
            $this->getJobService()->create($jobConfig);
        }

        /** Create default ActivityCategories: */
        $ecConfigs = $campType->getConfig(CampType::CNF_EVENT_CATEGORIES) ?: [];
        foreach ($ecConfigs as $ecConfig) {
            $ecConfig->campId = $camp->getId();
            $this->getActivityCategoryService()->create($ecConfig);
        }

        // Create Periods:
        if (isset($data->periods)) {
            foreach ($data->periods as $period) {
                $period->campId = $camp->getId();
                $this->getPeriodService()->create($period);
            }
        } elseif (isset($data->start, $data->end)) {
            $this->getPeriodService()->create((object) [
                'campId' => $camp->getId(),
                'description' => 'Main',
                'start' => $data->start,
                'end' => $data->end,
            ]);
        }

        return $camp;
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);

        if (isset($params['group'])) {
            $q->andWhere('row.owner = :group');
            $q->setParameter('group', $params['group']);
        }

        return $q;
    }
}
