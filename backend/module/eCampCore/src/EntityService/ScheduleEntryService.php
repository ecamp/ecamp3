<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Core\Hydrator\ScheduleEntryHydrator;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityValidationException;
use eCamp\Lib\Service\ServiceUtils;
use eCampApi\V1\Rest\ScheduleEntry\ScheduleEntryCollection;
use Laminas\Authentication\AuthenticationService;

class ScheduleEntryService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            ScheduleEntry::class,
            ScheduleEntryCollection::class,
            ScheduleEntryHydrator::class,
            $authenticationService
        );
    }

    /**
     * @throws EntityValidationException
     */
    protected function createEntity($data): ScheduleEntry {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = parent::createEntity($data);

        /** @var Period $period */
        $period = $this->findRelatedEntity(Period::class, $data, 'periodId');
        $period->addScheduleEntry($scheduleEntry);

        /** @var Activity $activity */
        $activity = $this->findRelatedEntity(Activity::class, $data, 'activityId');
        $activity->addScheduleEntry($scheduleEntry);

        if ($activity->getCamp()->getId() !== $period->getCamp()->getId()) {
            throw (new EntityValidationException())->setMessages([
                'activityId' => ['campMismatch' => 'Provided activity is not part of the same camp as provided period'],
                'periodId' => ['campMismatch' => 'Provided activity is not part of the same camp as provided period'],
            ]);
        }

        return $scheduleEntry;
    }

    protected function deleteEntity(BaseEntity $entity): void {
        /** @var ScheduleEntry $scheduleEntry */
        $scheduleEntry = $entity;
        $scheduleEntryId = $scheduleEntry->getId();
        $activity = $scheduleEntry->getActivity();

        $otherScheduleEntryExists =
            $activity->getScheduleEntries()->exists(function ($idx, $se) use ($scheduleEntryId) {
                return $se->getId() !== $scheduleEntryId;
            });

        parent::deleteEntity($entity);

        if (!$otherScheduleEntryExists) {
            $this->getServiceUtils()->emRemove($activity);
        }
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
        $q = parent::fetchAllQueryBuilder($params);
        $q->join('row.activity', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

        if (isset($params['activityId'])) {
            $q->andWhere('row.activity = :activityId');
            $q->setParameter('activityId', $params['activityId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id): QueryBuilder {
        $q = parent::fetchQueryBuilder($id);
        $q->join('row.activity', 'e');
        $q->andWhere($this->createFilter($q, Camp::class, 'e', 'camp'));

        return $q;
    }
}
