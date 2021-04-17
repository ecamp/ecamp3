<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
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
     * @throws NoAccessException
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    protected function createEntity($data): Day {
        /** @var Period $period */
        $period = $this->findRelatedEntity(Period::class, $data, 'periodId');

        /** @var Day $day */
        $day = parent::createEntity($data);
        $period->addDay($day);

        return $day;
    }

    protected function deleteEntity(BaseEntity $entity): void {
        parent::deleteEntity($entity);

        /** @var Day $day */
        $day = $entity;
        $day->getPeriod()->removeDay($day);
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
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

    protected function fetchQueryBuilder($id): QueryBuilder {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.period', 'p');
        $q->andWhere($this->createFilter($q, Camp::class, 'p', 'camp'));

        return $q;
    }
}
