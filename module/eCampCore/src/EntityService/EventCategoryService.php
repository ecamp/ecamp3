<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\EventCategoryHydrator;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventType;
use eCamp\Lib\Acl\NoAccessException;
use ZF\ApiProblem\ApiProblem;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\ServiceManager\EntityFilterManager;
use Zend\Hydrator\HydratorPluginManager;


class EventCategoryService extends AbstractEntityService {

    public function __construct(CampService $campService, EntityManager $entityManager, Acl $acl, EntityFilterManager $entityFilterManager, HydratorPluginManager $hydratorPluginManager) {
        parent::__construct(
            EventCategory::class,
            EventCategoryHydrator::class
        );

        /** 
         * necessary because manual injections (initializiers) seems not to run after lazy loading 
         * on the other hand, initializers seems to be discouraged anyway and regarded as bad practice.
         * more details on https://zendframework.github.io/zend-servicemanager/configuring-the-service-manager/
         * */
        
        $this->entityManager = $entityManager;
        $this->acl = $acl;
        $this->entityFilterManager = $entityFilterManager;
        $this->hydratorPluginmanager = $hydratorPluginManager;
     
        var_dump("EventCategoryService::_construct : Very expensive contructor.");
        var_dump("EventCategoryService::_construct : Now starting a call on a circular dependency.");
        $campService->fetchAll();

    }

    /**
     * @param mixed $data
     * @return EventCategory|ApiProblem
     * @throws ORMException
     * @throws NoAccessException
     */
    public function create($data) {
        /** @var EventCategory $eventCategory */
        $eventCategory = parent::create($data);

        /** @var EventType $eventType */
        $eventType = $this->findEntity(EventType::class, $data->event_type_id);
        $eventCategory->setEventType($eventType);

        /** @var Camp $camp */
        $camp = $this->findEntity(Camp::class, $data->camp_id);
        $camp->addEventCategory($eventCategory);

        return $eventCategory;
    }
}
