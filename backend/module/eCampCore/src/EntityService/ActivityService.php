<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Hydrator\ActivityHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class ActivityService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            Activity::class,
            ActivityHydrator::class,
            $authenticationService
        );
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
