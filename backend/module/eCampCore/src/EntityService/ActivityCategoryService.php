<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Hydrator\ActivityCategoryHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\EntityNotFoundException;
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

        /** @var Camp $camp */
        $camp = $this->findRelatedEntity(Camp::class, $data, 'campId');
        $camp->addActivityCategory($activityCategory);

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
