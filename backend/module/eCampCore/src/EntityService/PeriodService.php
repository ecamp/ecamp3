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
use eCamp\Lib\Types\DateUtc;
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

        // Validate start/end date
        $this->validateInput($period, $data);

        /** @var Period $period */
        $period = parent::patchEntity($period, $data);
        $this->updatePeriodDays($period);

        /** @var bool $moveScheduleEntries */
        $moveScheduleEntries = isset($data->moveScheduleEntries) ? $data->moveScheduleEntries : false;

        if (!$moveScheduleEntries) {
            $newStart = $period->getStart();
            $delta = $newStart->getTimestamp() - $oldStart->getTimestamp();
            $delta = $delta / 60;

            $this->updateScheduleEntries($period, $delta);
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
        $rows = $qb->getQuery()->getResult();

        if (count($rows) > 0) {
            $ex = new EntityValidationException();
            $ex->setMessages(['start' => ['noOverlap' => 'Periods may not overlap']]);

            throw $ex;
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
     * @throws EntityValidationException
     */
    private function validateInput(Period $period, $data) {
        /** @var bool $moveScheduleEntries */
        $moveScheduleEntries = isset($data->moveScheduleEntries) ? $data->moveScheduleEntries : false;

        // Validate Start / End
        if ($moveScheduleEntries) {
            // Validate length:
            $start = isset($data->start) ? new DateUtc($data->start) : $period->getStart();
            $end = isset($data->end) ? new DateUtc($data->end) : $period->getEnd();
            // length in minutes
            $length = $end->getTimestamp() - $start->getTimestamp();
            $length = ($length / 60) + 60 * 24;

            // check if there is a ScheduleEntry out of new bounds
            $periodToShort = $period->getScheduleEntries()->exists(function (int $idx, ScheduleEntry $se) use ($length) {
                return ($se->getPeriodOffset() + $se->getLength()) > $length;
            });

            if ($periodToShort) {
                throw (new EntityValidationException())->setMessages([
                    'end' => ['toShort' => 'Period is to short'],
                ]);
            }
        } else {
            $start = isset($data->start) ? new DateUtc($data->start) : $period->getStart();
            $end = isset($data->end) ? new DateUtc($data->end) : $period->getEnd();

            $periodEndsTooEarly = $period->getScheduleEntries()->exists(function (int $idx, ScheduleEntry $se) use ($period, $end) {
                $seEndTimestamp = $period->getStart()->getTimestamp() + 60 * ($se->getPeriodOffset() + $se->getLength());

                return $end->getTimestamp() <= $seEndTimestamp;
            });

            $periodStartsTooLate = $period->getScheduleEntries()->exists(function (int $idx, ScheduleEntry $se) use ($period, $start) {
                $seStartTimestamp = $period->getStart()->getTimestamp() + 60 * $se->getPeriodOffset();

                return $start->getTimestamp() >= $seStartTimestamp;
            });

            if ($periodEndsTooEarly || $periodStartsTooLate) {
                $messages = [];
                if ($periodEndsTooEarly) {
                    $messages['end'] = ['toShort' => 'Period is ends to early.'];
                }
                if ($periodStartsTooLate) {
                    $messages['start'] = ['lateStart' => 'Period starts to late.'];
                }

                throw (new EntityValidationException())->setMessages($messages);
            }
        }
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
