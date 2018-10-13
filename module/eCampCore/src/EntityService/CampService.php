<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Hydrator\CampHydrator;
use eCamp\Core\Entity\AbstractCampOwner;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\User;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use ZF\ApiProblem\ApiProblem;

class CampService extends AbstractEntityService {
    
    /** @var PeriodService */
    protected $periodService;

    /** @var EventCategoryService */
    protected $eventCategoryService;

    public function __construct
    (   EventCategoryService $eventCategoryService,
        PeriodService $periodService,
        ServiceUtils $serviceUtils
    ) {
        parent::__construct(
            $serviceUtils,
            Camp::class,
            CampHydrator::class
        );

        $this->periodService = $periodService;
        $this->eventCategoryService = $eventCategoryService;
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
        $owner->addOwnedCamp($camp);

        /** Create default Jobs */
        $jobConfigs = $campType->getConfig(CampType::CNF_JOBS) ?: [];
        foreach ($jobConfigs as $jobConfig) {
            $jobConfig->camp_id = $camp->getId();
            $this->getJobService()->create($jobConfig);
        }

        /** Create default EventCategories: */
        $ecConfigs = $campType->getConfig(CampType::CNF_EVENT_CATEGORIES) ?: [];
        foreach ($ecConfigs as $ecConfig) {
            $ecConfig->camp_id = $camp->getId();
            $this->getEventCategoryService()->create($ecConfig);
        }

        /** Create Periods: */
        if (isset($data->periods)) {
            foreach ($data->periods as $period) {
                $period->camp_id = $camp->getId();
                $this->getPeriodService()->create($period);
            }
        } elseif (isset($data->start, $data->end)) {
            $this->getPeriodService()->create((object)[
                'camp_id' => $camp->getId(),
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
