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
    public function __construct(
        ServiceUtils $serviceUtils,
        string $entityClassname,
        string $hydratorClassname,
        AuthenticationService $authenticationService
    ) {
        parent::__construct($serviceUtils, $entityClassname, $hydratorClassname, $authenticationService);
    }

    /**
     * Returns a single plugin entity attached to $eventPluginId (1:1 connection).
     *
     * @param mixed $eventPluginid
     * @param mixed $eventPluginId
     *
     * @return BaseEntity
     */
    public function fetchFromEventPlugin($eventPluginId) {
        return $this->fetchAllFromEventPlugin($eventPluginId)[0] ?? null;
    }

    /**
     * Returns all plugin entities attached to $eventPluginId (1:n connection).
     *
     * @param string $eventPluginId
     *
     * @return array
     */
    public function fetchAllFromEventPlugin($eventPluginId) {
        $q = $this->fetchAllQueryBuilder(['event_plugin_id' => $eventPluginId]);

        return $this->getQueryResult($q);
    }

    /**
     * @param array $data
     *
     * @throws NoAccessException
     * @throws ORMException
     *
     * @return ApiProblem|BasePluginEntity
     */
    public function create($data, bool $persist = true, ?EventPlugin $eventPlugin = null) {
        /** @var BasePluginEntity $entity */
        $entity = parent::create($data, false);

        if (isset($data['event_plugin_id'])) {
            /** @var EventPlugin $eventPlugin */
            $eventPlugin = $this->findEntity(EventPlugin::class, $data['event_plugin_id']);
            $entity->setEventPlugin($eventPlugin);
        } elseif ($eventPlugin) {
            $entity->setEventPlugin($eventPlugin);
        }

        if ($persist) {
            $this->getServiceUtils()->emPersist($entity);
        }

        return $entity;
    }

    /**
     * @param string $className
     *
     * @return ApiProblem|BasePluginEntity
     */
    protected function createEntity($className) {
        // @var BasePluginEntity $entity
        return parent::createEntity($className);
    }

    protected function fetchQueryBuilder($id) {
        return parent::fetchQueryBuilder($id);
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);

        if (isset($params['event_plugin_id'])) {
            $q->andWhere('row.eventPlugin = :eventPlugin_id');
            $q->setParameter('eventPlugin_id', $params['event_plugin_id']);
        }

        return $q;
    }
}
