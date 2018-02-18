<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use eCamp\Core\Hydrator\DayHydrator;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\Period;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\BaseService;
use ZF\ApiProblem\ApiProblem;

class DayService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , DayHydrator $dayHydrator
    ) {
        parent::__construct($acl, $entityManager, $dayHydrator, Day::class);
    }


    public function findCollectionQueryBuilder($className, $alias, $params = []) {
        $q = parent::findCollectionQueryBuilder($className, $alias, $params);

        $periodId = $params['period_id'];
        if ($periodId) {
            $q->andWhere('row.period = :periodId');
            $q->setParameter('periodId', $periodId);
        }

        $q->orderBy('row.period, row.dayOffset');

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
