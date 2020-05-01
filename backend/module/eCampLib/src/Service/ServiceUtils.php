<?php

namespace eCamp\Lib\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\EntityFilter\EntityFilterInterface;
use eCamp\Lib\ServiceManager\EntityFilterManager;
use Zend\Hydrator\HydratorInterface;
use Zend\Hydrator\HydratorPluginManager;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

class ServiceUtils {
    /** @var EntityManager */
    private $entityManager;
    
    /** @var Acl */
    private $acl;

    /** @var EntityFilterManager */
    private $entityFilterManager;

    /** @var HydratorPluginManager */
    private $hydratorPluginManager;

    public function __construct(
        Acl $acl,
        EntityManager $entityManager,
        EntityFilterManager $entityFilterManager,
        HydratorPluginManager $hydratorPluginManager
    ) {
        $this->acl = $acl;
        $this->entityManager = $entityManager;
        $this->entityFilterManager = $entityFilterManager;
        $this->hydratorPluginManager = $hydratorPluginManager;
    }

    /**
     * @param RoleInterface|string     $role
     * @param ResourceInterface|string $resource
     * @param string                   $privilege
     *
     * @return bool
     */
    public function aclIsAllowed($role = null, $resource = null, $privilege = null) {
        return $this->acl->isAllowed($role, $resource, $privilege);
    }

    /**
     * @param RoleInterface|string     $role
     * @param ResourceInterface|string $resource
     * @param string                   $privilege
     *
     * @throws NoAccessException
     */
    public function aclAssertAllowed($role = null, $resource = null, $privilege = null) {
        $this->acl->assertAllowed($role, $resource, $privilege);
    }

    /**
     * @return QueryBuilder
     */
    public function emCreateQueryBuilder() {
        return $this->entityManager->createQueryBuilder();
    }

    /**
     * @param $entityName
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function emGetRepository($entityName) {
        return $this->entityManager->getRepository($entityName);
    }

    /**
     * @param BaseEntity $entity
     *
     * @return array
     */
    public function emGetOrigEntityData($entity) {
        $uow = $this->entityManager->getUnitOfWork();

        return $uow->getOriginalEntityData($entity);
    }

    /**
     * @throws ORMException
     */
    public function emPersist(BaseEntity $entity) {
        $this->entityManager->persist($entity);
    }

    /**
     * @throws ORMException
     */
    public function emRemove(BaseEntity $entity) {
        $this->entityManager->remove($entity);
    }

    /**
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function emFlush() {
        $this->entityManager->flush();
    }

    /**
     * @param $className
     *
     * @return EntityFilterInterface
     */
    public function getEntityFilter($className) {
        return $this->entityFilterManager->getByEntityClass($className);
    }

    /**
     * @param $name
     *
     * @return HydratorInterface
     */
    public function getHydrator($name, array $options = null) {
        return $this->hydratorPluginManager->get($name, $options);
    }
}
