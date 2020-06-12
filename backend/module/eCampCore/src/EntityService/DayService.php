<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\Period;
use eCamp\Core\Hydrator\DayHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class DayService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            Day::class,
            DayHydrator::class,
            $authenticationService
        );
    }

    /**
     * @param mixed $data
     *
     * @throws NoAccessException
     * @throws EntityNotFoundException
     * @throws ORMException
     *
     * @return Day
     */
    protected function createEntity($data) {
        /** @var Period $period */
        $period = $this->findEntity(Period::class, $data->periodId);

        /** @var Day $day */
        $day = parent::create($data);
        $period->addDay($day);

        return $day;
    }

    /**
     * @return Day
     */
    protected function deleteEntity(BaseEntity $entity) {
        /** @var Day $day */
        $day = parent::deleteEntity($entity);
        $day->getPeriod()->removeDay($day);

        return $day;
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->join('row.period', 'p');
        $q->andWhere($this->createFilter($q, Camp::class, 'p', 'camp'));

        if (isset($params['campId'])) {
            $q->andWhere('p.camp = :campId');
            $q->setParameter('campId', $params['campId']);
        }

        if (isset($params['periodId'])) {
            $q->andWhere('row.period = :periodId');
            $q->setParameter('periodId', $params['periodId']);
        }

        $q->orderBy('row.period, row.dayOffset');

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.period', 'p');
        $q->andWhere($this->createFilter($q, Camp::class, 'p', 'camp'));

        return $q;
    }
}
