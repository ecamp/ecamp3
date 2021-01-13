<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\AbstractCampOwner;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
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

    /** @var CampCollaborationService */
    protected $campCollaboratorService;

    public function __construct(
        ActivityCategoryService $activityCategoryService,
        PeriodService $periodService,
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        CampCollaborationService $campCollaboratorService
    ) {
        parent::__construct(
            $serviceUtils,
            Camp::class,
            CampHydrator::class,
            $authenticationService
        );

        $this->periodService = $periodService;
        $this->activityCategoryService = $activityCategoryService;
        $this->campCollaboratorService = $campCollaboratorService;
    }

    /**
     * @return ApiProblem|array
     */
    public function fetchByOwner(AbstractCampOwner $owner) {
        $q = parent::findCollectionQueryBuilder(Camp::class, 'row', null);
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
        $this->assertAuthenticated();

        /** @var AbstractCampOwner $owner */
        $owner = $this->getAuthUser();
        if (isset($data->ownerId)) {
            $owner = $this->findEntity(AbstractCampOwner::class, $data->ownerId);
        }

        /** @var User $creator */
        $creator = $this->getAuthUser();

        /** @var Camp $camp */
        $camp = parent::createEntity($data);
        $camp->setName($data->name);
        $camp->setCreator($creator);
        $owner->addOwnedCamp($camp);

        return $camp;
    }

    protected function createEntityPost(BaseEntity $entity, $data) {
        /** @var Camp $camp */
        $camp = $entity;

        // Create CampCollaboration for Creator
        $this->campCollaboratorService->create((object) [
            'campId' => $camp->getId(),
            'role' => CampCollaboration::ROLE_MANAGER,
        ]);

        // Create default Jobs
        /*
        $jobConfigs = $campType->getConfig(CampType::CNF_JOBS) ?: [];
        foreach ($jobConfigs as $jobConfig) {
            $jobConfig->campId = $camp->getId();
            $this->getJobService()->create($jobConfig);
        }
        */

        // TODO
        // Create ActivityCategory from ActivityCategoryTemplate
        // Create default ActivityCategories
        // $acConfigs = $campType->getConfig(CampType::CNF_ACTIVITY_CATEGORIES) ?: [];
        // foreach ($acConfigs as $acConfig) {
        //     $acConfig->campId = $camp->getId();
        //     $this->activityCategoryService->create($acConfig);
        // }

        // Create Periods:
        if (isset($data->periods)) {
            foreach ($data->periods as $period) {
                $period = (object) $period;
                $period->campId = $camp->getId();
                $this->periodService->create($period);
            }
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
