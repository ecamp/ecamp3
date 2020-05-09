<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\AbstractCampOwner;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\User;
use eCamp\Core\Hydrator\CampHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;
use ZF\ApiProblem\ApiProblem;

class CampService extends AbstractEntityService {
    /** @var PeriodService */
    protected $periodService;

    /** @var EventCategoryService */
    protected $eventCategoryService;

    public function __construct(
        EventCategoryService $eventCategoryService,
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
        $this->eventCategoryService = $eventCategoryService;
    }

    /**
     * @param mixed $data
     * @param mixed $persist
     *
     * @throws ORMException
     * @throws NoAccessException
     *
     * @return ApiProblem|Camp
     */
    public function create($data, bool $persist = true) {
        $this->assertAllowed(Camp::class, __FUNCTION__);

        /** @var CampType $campType */
        $campType = $this->findEntity(CampType::class, $data->campTypeId);

        /** @var AbstractCampOwner $owner */
        $owner = $this->findEntity(AbstractCampOwner::class, $data->ownerId);

        /** @var User $creator */
        $creator = $this->getAuthUser();

        /** @var Camp $camp */
        $camp = parent::create($data, $persist);
        $camp->setCampType($campType);
        $camp->setCreator($creator);
        $owner->addOwnedCamp($camp);

        /** Create default Jobs */
        $jobConfigs = $campType->getConfig(CampType::CNF_JOBS) ?: [];
        foreach ($jobConfigs as $jobConfig) {
            $jobConfig->campId = $camp->getId();
            $this->getJobService()->create($jobConfig);
        }

        /** Create default EventCategories: */
        $ecConfigs = $campType->getConfig(CampType::CNF_EVENT_CATEGORIES) ?: [];
        foreach ($ecConfigs as $ecConfig) {
            $ecConfig->campId = $camp->getId();
            $this->getEventCategoryService()->create($ecConfig);
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

    /**
     * @return ApiProblem|array
     */
    public function fetchByOwner(AbstractCampOwner $owner) {
        $q = parent::findCollectionQueryBuilder(Camp::class, 'row');
        $q->where('row.owner = :owner');
        $q->setParameter('owner', $owner);

        return $this->getQueryResult($q);
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
