<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Core\Hydrator\ScheduleEntryHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class ScheduleEntryService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            ScheduleEntry::class,
            ScheduleEntryHydrator::class,
            $authenticationService
        );
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->join('row.activity', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

        if (isset($params['activityId'])) {
            $q->andWhere('row.activity = :activityId');
            $q->setParameter('activityId', $params['activityId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.activity', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

        return $q;
    }
}
