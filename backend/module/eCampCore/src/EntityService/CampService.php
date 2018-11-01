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
use Zend\Paginator\Paginator;
use ZF\ApiProblem\ApiProblem;

class CampService extends AbstractEntityService {
    
    /** @var PeriodService */
    protected $periodService;

    /** @var JobService  */
    protected $jobService;

    /** @var EventCategoryService */
    protected $eventCategoryService;

    public function __construct
    (   PeriodService $periodService,
        JobService $jobService,
        EventCategoryService $eventCategoryService,
        ServiceUtils $serviceUtils
    ) {
        parent::__construct(
            $serviceUtils,
            Camp::class,
            CampHydrator::class
        );

        $this->periodService = $periodService;
        $this->jobService = $jobService;
        $this->eventCategoryService = $eventCategoryService;
    }


    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);

        $user = $this->getEntityFromData(User::class, $params, 'user');
        if ($user != null) {
            $q->andWhere($q->expr()->orX('row.owner = :user', 'row.creator = :user'));
            $q->setParameter('user', $user);
        }

        $owner = $this->getEntityFromData(AbstractCampOwner::class, $params, 'owner');
        if ($owner != null) {
            $q->andWhere('row.owner = :owner');
            $q->setParameter('owner', $owner);
        }

        $creator = $this->getEntityFromData(User::class, $params, 'creator');
        if ($creator != null) {
            $q->andWhere('row.creator = :creator');
            $q->setParameter('creator', $creator);
        }

        return $q;
    }


    /**
     * @swaggerTitle FetchAll Camps
     * @swaggerQuery AbstractCampOwner owner_id (Camp Owner)
     * @swaggerQuery User creator_id (Camp Creator)
     * @swaggerQuery User user_id (Camp Creator or Camp Owner)
     *
     * @param array $params
     * @return Paginator|ApiProblem
     */
    public function fetchAll($params = []) {
        return parent::fetchAll($params);
    }

    /**
     * @swaggerTitle Fetch Camp
     * @swaggerPath string id CampId
     *
     * @param mixed $id
     * @return Camp|ApiProblem
     */
    public function fetch($id) {
        return parent::fetch($id);
    }

    /**
     * @swaggerTitle Create Camp
     * @swaggerBody string camp_type_id CampTypeId
     * @swaggerBody string owner_id     Group|User
     *
     * @param $data
     * @return Camp|ApiProblem
     * @throws NoAccessException
     * @throws ORMException
     */
    public function create($data) {
        $this->assertAllowed(Camp::class, __FUNCTION__);

        /** @var CampType $campType */
        $campType = $this->getEntityFromData(CampType::class, $data, 'camp_type');

        /** @var AbstractCampOwner $owner */
        $owner = $this->getEntityFromData(AbstractCampOwner::class, $data, 'owner');

        /** @var User $creator */
        $creator = $this->getAuthUser();

        /** @var Camp $camp */
        $camp = parent::create($data);
        $camp->setCampType($campType);
        $camp->setCreator($creator);
        $camp->setName($data->name);
        $owner->addOwnedCamp($camp);

        /** Create default Jobs */
//        $jobConfigs = $campType->getConfig(CampType::CNF_JOBS) ?: [];
//        foreach ($jobConfigs as $jobConfig) {
//            $jobConfig->camp = $camp;
//            $this->jobService->create($jobConfig);
//        }

        /** Create default EventCategories: */
        $ecConfigs = $campType->getConfig(CampType::CNF_EVENT_CATEGORIES) ?: [];
        foreach ($ecConfigs as $ecConfig) {
            $ecConfig->camp = $camp;
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
}
