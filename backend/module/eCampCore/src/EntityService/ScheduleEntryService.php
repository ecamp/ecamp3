<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Core\Hydrator\ScheduleEntryHydrator;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\EntityValidationException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

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

    /**
     * @param mixed $data
     *
     * @throws ORMException
     * @throws NoAccessException
     * @throws EntityNotFoundException
     *
     * @return ScheduleEntry
     */
    protected function createEntity($data) {
        //throw new \Error('test');

        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = parent::createEntity($data);

        try {
            /** @var Period $period */
            $period = $this->findEntity(Period::class, $data->periodId);
            $period->addScheduleEntry($scheduleEntry);
        } catch (EntityNotFoundException $e) {
            $ex = new EntityValidationException();
            $ex->setMessages(['periodId' => ['notFound' => "Provided period with id '{$data->periodId}' was not found"]]);

            throw $ex;
        }

        try {
            /** @var Activity $activity */
            $activity = $this->findEntity(Activity::class, $data->activityId);
            $activity->addScheduleEntry($scheduleEntry);
        } catch (EntityNotFoundException $e) {
            $ex = new EntityValidationException();
            $ex->setMessages(['activityId' => ['notFound' => "Provided activity with id '{$data->activityId}' was not found"]]);

            throw $ex;
        }

        if ($activity->getCamp()->getId() !== $period->getCamp()->getId()) {
            $ex = new EntityValidationException();
            $ex->setMessages([
                'activityId' => ['campMismatch' => 'Provided activity is not part of the same camp as provided period'],
                'periodId' => ['campMismatch' => 'Provided activity is not part of the same camp as provided period'],
            ]);

            throw $ex;
        }

        return $scheduleEntry;
    }
}
