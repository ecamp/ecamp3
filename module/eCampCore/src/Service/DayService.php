<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Hydrator\DayHydrator;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\Period;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\BaseService;
use ZF\ApiProblem\ApiProblem;

class DayService extends BaseService {
    public function __construct(DayHydrator $dayHydrator) {
        parent::__construct($dayHydrator, Day::class);
    }


    public function findCollectionQueryBuilder($className, $alias, $params = []) {
        $q = parent::findCollectionQueryBuilder($className, $alias);

        $periodId = $params['period_id'];
        if ($periodId) {
            $q->andWhere('row.period = :periodId');
            $q->setParameter('periodId', $periodId);
        }

        $q->orderBy('row.period, row.dayOffset');

        return $q;
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->join('row.period', 'p');
        $q->andWhere($this->createFilter($q, Camp::class, 'p', 'camp'));

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.period', 'p');
        $q->andWhere($this->createFilter($q, Camp::class, 'p', 'camp'));

        return $q;
    }


    /**
     * @param mixed $data
     * @return Day|ApiProblem
     * @throws ORMException
     * @throws NoAccessException
     */
    public function create($data) {
        /** @var Period $period */
        $period = $this->findEntity(Period::class, $data->period_id);

        /** @var Day $day */
        $day = parent::create($data);
        $period->addDay($day);

        return $day;
    }


    /**
     * @param mixed $id
     * @return bool|null|ApiProblem
     * @throws NoAccessException
     * @throws ORMException
     */
    public function delete($id) {
        /** @var Day $day */
        $day = $this->fetch($id);
        $period = $day->getPeriod();
        $period->removeDay($day);

        return parent::delete($id);
    }
}
