<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Core\Hydrator\PeriodHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\EntityValidationException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class PeriodService extends AbstractEntityService {
    protected DayService $dayService;
    protected ScheduleEntryService $scheduleEntryService;

    public function __construct(
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        DayService $dayService,
        ScheduleEntryService $scheduleEntryService
    ) {
        parent::__construct(
            $serviceUtils,
            Period::class,
            PeriodHydrator::class,
            $authenticationService
        );

        $this->dayService = $dayService;
        $this->scheduleEntryService = $scheduleEntryService;
    }

    /**
     * @throws ORMException
     * @throws EntityNotFoundException
     * @throws NoAccessException
     */
    protected function createEntity($data): Period {
        /** @var Camp $camp */
        $camp = $this->findRelatedEntity(Camp::class, $data, 'campId');

        /** @var Period $period */
        $period = parent::createEntity($data);

        $camp->addPeriod($period);

        return $period;
    }

    /**
     * @param $data
     *
     * @throws NoAccessException
     * @throws ORMException
     */
    protected function createEntityPost(BaseEntity $entity, $data): Period {
        /** @var Period $period */
        $period = $entity;

        $durationInDays = $period->getDurationInDays();
        for ($idx = 0; $idx < $durationInDays; ++$idx) {
            $this->dayService->create((object) [
                'periodId' => $period->getId(),
                'dayOffset' => $idx,
            ]);
        }

        return $period;
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     */
    protected function patchEntity(BaseEntity $entity, $data): Period {
        /** @var Period $period */
        $period = $entity;
        $oldStart = $period->getStart();

        /** @var Period $period */
        $period = parent::patchEntity($period, $data);
        $this->updatePeriodDays($period);

        /** @var bool $moveScheduleEntries */
        $moveScheduleEntries = isset($data->moveScheduleEntries) ? $data->moveScheduleEntries : false;

        if (!$moveScheduleEntries) {
            $newStart = $period->getStart();
            $deltaSec = $newStart->getTimestamp() - $oldStart->getTimestamp();
            $deltaMin = $deltaSec / 60;

            $this->updateScheduleEntries($period, $deltaMin);
        }

        return $period;
    }

    protected function deleteEntity(BaseEntity $entity): void {
        /** @var Period $period */
        $period = $entity;
        $period->getCamp()->removePeriod($period);

        parent::deleteEntity($entity);
    }

    /**
     * @param $entity
     */
    protected function validateEntity(BaseEntity $entity): void {
        /** @var Period $period */
        $period = $entity;
        $errors = [];

        // Check for any ScheduleEntry starting before period starts
        $periodStartsTooLate = $period->getScheduleEntries()->exists(function (int $idx, ScheduleEntry $se) {
            return $se->getPeriodOffset() < 0;
        });
        if ($periodStartsTooLate) {
            $errors += ['start' => ['startsTooLate' => 'period starts too late']];
        }

        // Check for any ScheduleEntry ending after period ends
        $periodEndsTooEarly = $period->getScheduleEntries()->exists(function (int $idx, ScheduleEntry $se) use ($period) {
            return ($se->getPeriodOffset() + $se->getLength()) > ($period->getDurationInDays() * 60 * 24);
        });
        if ($periodEndsTooEarly) {
            $errors += ['end' => ['endsTooEarly' => 'period is too short']];
        }

        // Chcek for other overlapping Period
        $qb = $this->findCollectionQueryBuilder(Period::class, 'p', null)
            ->where('p.camp = :camp')
            ->andWhere('p.id != :id')
            ->andWhere('p.start <= :end')
            ->andWhere('p.end >= :start')
            ->setParameter('camp', $period->getCamp()->getId())
            ->setParameter('id', $period->getId())
            ->setParameter('start', $period->getStart())
            ->setParameter('end', $period->getEnd())
        ;
        $periodOverlapsOtherPeriod = count($qb->getQuery()->getResult()) > 0;
        if ($periodOverlapsOtherPeriod) {
            $errors += ['start' => ['noOverlap' => 'periods may not overlap']];
        }

        if (count($errors)) {
            throw (new EntityValidationException())->setMessages($errors);
        }
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
        $q = parent::fetchAllQueryBuilder($params);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        if (isset($params['campId'])) {
            $q->andWhere('row.camp = :campId');
            $q->setParameter('campId', $params['campId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id): QueryBuilder {
        $q = parent::fetchQueryBuilder($id);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        return $q;
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     */
    private function updatePeriodDays(Period $period): void {
        $days = $period->getDays();
        $daysCountNew = $period->getDurationInDays();

        $daysToDelete = $days->filter(function (Day $day) use ($daysCountNew) {
            return $day->getDayOffset() >= $daysCountNew;
        });

        foreach ($daysToDelete as $day) {
            // @var Day $day
            $this->dayService->delete($day->getId());
        }

        for ($idx = 0; $idx < $daysCountNew; ++$idx) {
            $day = $days->filter(function (Day $day) use ($idx) {
                return $day->getDayOffset() == $idx;
            });

            if ($day->isEmpty()) {
                $this->dayService->create((object) [
                    'periodId' => $period->getId(),
                    'dayOffset' => $idx,
                ]);
            }
        }
    }

    /**
     * @throws NoAccessException
     */
    private function updateScheduleEntries(Period $period, int $delta): void {
        $scheduleEntries = $period->getScheduleEntries();
        foreach ($scheduleEntries as $scheduleEntry) {
            // @var ScheduleEntry $scheduleEntry
            $this->scheduleEntryService->patch($scheduleEntry->getId(), (object) [
                'periodOffset' => $scheduleEntry->getPeriodOffset() - $delta,
            ]);
        }
    }
}
