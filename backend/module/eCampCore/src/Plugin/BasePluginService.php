<?php

namespace eCamp\Core\Plugin;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\EntityService\AbstractEntityService;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;
use ZF\ApiProblem\ApiProblem;

abstract class BasePluginService extends AbstractEntityService {

    /** @var string */
    private $eventPluginId;

    /** @var EventPlugin */
    private $eventPlugin;

    public function __construct(
        ServiceUtils $serviceUtils,
        string $entityClassname,
        string $hydratorClassname,
        AuthenticationService $authenticationService,
        ? string $eventPluginId
    ) {
        parent::__construct($serviceUtils, $entityClassname, $hydratorClassname, $authenticationService);

        $this->eventPluginId = $eventPluginId;
    }


    /** @return string */
    protected function getEventPluginId() {
        return $this->eventPluginId;
    }

    /** @return EventPlugin */
    protected function getEventPlugin() {
        if ($this->eventPlugin == null) {
            if ($this->eventPluginId != null) {
                $this->eventPlugin = $this->findEntity(EventPlugin::class, $this->eventPluginId);
            }
        }
        return $this->eventPlugin;
    }

    /**
     * Returns a single plugin entity attached to $eventPluginId (1:1 connection)
     * @param mixed $eventPluginid
     * @return BaseEntity
     */
    public function fetchFromEventPlugin($eventPluginId) {
        return $this->fetchAllFromEventPlugin($eventPluginId)[0];
    }

    /**
     * Returns all plugin entities attached to $eventPluginId (1:n connection)
     * @param string $eventPluginId
     * @return array
     */
    public function fetchAllFromEventPlugin($eventPluginId) {
        $q = $this->fetchAllQueryBuilder(['event_plugin_id' => $eventPluginId]);
        $list = $this->getQueryResult($q);
        return $list;
    }


    /**
     * @param string $className
     * @return BasePluginEntity|ApiProblem
     */
    protected function createEntity($className) {
        /** @var BasePluginEntity $entity */
        $entity = parent::createEntity($className);

        if ($entity instanceof ApiProblem) {
            return $entity;
        }

        if ($this->getEventPlugin() != null) {
            $entity->setEventPlugin($this->getEventPlugin());
        }

        return $entity;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);

        if (is_subclass_of($this->entityClass, BasePluginEntity::class)) {
            if ($this->eventPluginId !== null) {
                $q->andWhere('row.eventPlugin = :eventPluginId');
                $q->setParameter('eventPluginId', $this->eventPluginId);
            }
        }

        return $q;
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);

        if (is_subclass_of($this->entityClass, BasePluginEntity::class)) {
            if ($this->eventPluginId !== null) {
                $q->andWhere('row.eventPlugin = :eventPluginId');
                $q->setParameter('eventPluginId', $this->eventPluginId);
            }
        }

        if (isset($params['event_plugin_id'])) {
            $q->andWhere('row.eventPlugin = :eventPlugin_id');
            $q->setParameter('eventPlugin_id', $params['event_plugin_id']);
        }

        return $q;
    }



    /**
     * @param mixed $data
     * @return BasePluginEntity|ApiProblem
     * @throws ORMException
     * @throws NoAccessException
     */
    public function create($data, ?EventPlugin $eventPlugin) {
        /** @var BasePluginEntity $entity */
        $entity = parent::create($data);

        if ($eventPlugin) {
            $entity->setEventPlugin($eventPlugin);
        } elseif ($data->event_plugin_id) {
            /** @var EventPlugin $eventPlugin */
            $eventPlugin = $this->findEntity(EventPlugin::class, $data->event_plugin_id);
            $entity->setEventPlugin($eventPlugin);
        } else {
            throw new \Error("Cannot create plugin data without defining EventPlugin");
        }

        $em = $this->getServiceUtils()->entityManager;
        // $this->getServiceUtils()->emFlush();

        return $entity;
    }
}
