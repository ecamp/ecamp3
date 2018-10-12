<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\ORMException;
use eCamp\Core\Hydrator\CampHydrator;
use eCamp\Core\Entity\AbstractCampOwner;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\User;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\BaseService;
use ZF\ApiProblem\ApiProblem;

class CampService extends BaseService {
    /** @var JobService */
    private $jobService;

    /** @var EventCategoryService */
    private $eventCategoryService;

    /** @var PeriodService */
    private $periodService;


    public function __construct(
        CampHydrator $campHydrator,
        JobService $jobService,
        EventCategoryService $eventCategoryService,
        PeriodService $periodService
    ) {
        parent::__construct($campHydrator, Camp::class);

        $this->jobService = $jobService;
        $this->eventCategoryService = $eventCategoryService;
        $this->periodService = $periodService;
    }


    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);

        if (isset($params['group'])) {
            $q->andWhere('row.owner = :group');
            $q->setParameter('group', $params['group']);
        }

        return $q;
    }



    /**
     * @param mixed $data
     * @return Camp|ApiProblem
     * @throws ORMException
     * @throws NoAccessException
     */
    public function create($data) {
        $this->assertAllowed(Camp::class, __FUNCTION__);

        /** @var CampType $campType */
        $campType = $this->findEntity(CampType::class, $data->camp_type_id);

        /** @var AbstractCampOwner $owner */
        $owner = $this->findEntity(AbstractCampOwner::class, $data->owner_id);

        /** @var User $creator */
        $creator = $this->getAuthUser();

        /** @var Camp $camp */
        $camp = parent::create($data);
        $camp->setCampType($campType);
        $camp->setCreator($creator);
        $camp->setName($data->name);
        $owner->addOwnedCamp($camp);

        /** Create default Jobs */
        $jobConfigs = $campType->getConfig(CampType::CNF_JOBS) ?: [];
        foreach ($jobConfigs as $jobConfig) {
            $jobConfig->camp_id = $camp->getId();
            $this->jobService->create($jobConfig);
        }

        /** Create default EventCategories: */
        $ecConfigs = $campType->getConfig(CampType::CNF_EVENT_CATEGORIES) ?: [];
        foreach ($ecConfigs as $ecConfig) {
            $ecConfig->camp_id = $camp->getId();
            $this->eventCategoryService->create($ecConfig);
        }

        /** Create Periods: */
        if (isset($data->periods)) {
            foreach ($data->periods as $period) {
                $period = (object)$period;
                $period->camp = $camp;
                $this->periodService->create($period);
            }
        } elseif (isset($data->start, $data->end)) {
            $this->periodService->create((object)[
                'camp' => $camp,
                'description' => 'Main',
                'start' => $data->start,
                'end' => $data->end
            ]);
        }

        return $camp;
    }


    /**
     * @param AbstractCampOwner $owner
     * @return array|ApiProblem
     */
    public function fetchByOwner(AbstractCampOwner $owner) {
        $q = parent::findCollectionQueryBuilder(Camp::class, 'row');
        $q->where('row.owner = :owner');
        $q->setParameter('owner', $owner);

        return $this->getQueryResult($q);
    }
}
