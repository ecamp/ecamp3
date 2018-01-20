<?php

namespace eCamp\Lib\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\User;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use Zend\EventManager\EventManagerInterface;
use Zend\Hydrator\HydratorInterface;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

abstract class BaseService extends AbstractResourceListener
{
    /** @var Acl */
    private $acl;

    /** @var EntityManager */
    private $entityManager;

    /** @var EntityRepository */
    private $repository;

    /** @var string */
    private $entityClassName;

    /** @var string */
    private $hydratorClassName;

    /** @var HydratorInterface */
    private $hydrator;


    public function __construct
    (   Acl $acl
    ,   EntityManager $entityManager
    ,   $entityClassName
    ,   $hydratorClassName
    ) {
        $this->acl = $acl;
        $this->entityManager = $entityManager;
        $this->entityClassName = $entityClassName;
        $this->hydratorClassName = $hydratorClassName;
    }


    /** @return EntityManager */
    protected function getEntityManager() {
        return $this->entityManager;
    }

    /** @return EntityRepository */
    protected function getRepository() {
        if ($this->repository == null) {
            $this->repository = $this->entityManager->getRepository($this->entityClassName);
        }
        return $this->repository;
    }

    /** @return HydratorInterface */
    protected function getHydrator() {
        if ($this->hydrator == null) {
            $hydratorClassName = $this->hydratorClassName;
            $this->hydrator = new $hydratorClassName();
        }
        return $this->hydrator;
    }

    /**
     * @return null|User|ApiProblem
     */
    protected function getAuthUser() {
        // TODO: Use AuthService
        $userRepo = $this->entityManager->getRepository(User::class);
        $users = $userRepo->findAll();

        reset($users);
        $user = current($users);

        return $user;
    }

    /**
     * @param null $resource
     * @param null $privilege
     * @return bool
     */
    protected function isAllowed($resource, $privilege = null) {
        $user = $this->getAuthUser();
        return $this->acl->isAllowed($user, $resource, $privilege);
    }

    /**
     * @param null $resource
     * @param null $privilege
     * @throws \eCamp\Lib\Acl\NoAccessException
     */
    protected function assertAllowed($resource, $privilege = null) {
        $user = $this->getAuthUser();
        return $this->acl->assertAllowed($user, $resource, $privilege);
    }

    /**
     * @param string $className
     * @return object|ApiProblem
     */
    protected function createEntity($className) {
        $entity = null;
        try {
            $entity = new $className();
        } catch (\Exception $e) {
            return new ApiProblem(500, $e->getMessage());
        }
        return $entity;
    }

    /**
     * @param string $className
     * @param string $id
     * @return null|object|ApiProblem
     */
    protected function findEntity($className, $id) {
        $entity = null;
        try {
            $entity = $this->getEntityManager()->find($className, $id);
        } catch (\Exception $e) {
            return new ApiProblem(500, $e->getMessage());
        }
        return $entity;
    }


    public function attach(EventManagerInterface $events, $priority = 1) {
        parent::attach($events, $priority);
        
        $dbFlush = function() { $this->entityManager->flush(); };
        $this->listeners[] = $events->attach('create', $dbFlush, -100);
        $this->listeners[] = $events->attach('delete', $dbFlush, -100);
        $this->listeners[] = $events->attach('deleteList', $dbFlush, -100);
        $this->listeners[] = $events->attach('patch', $dbFlush, -100);
        $this->listeners[] = $events->attach('patchList', $dbFlush, -100);
        $this->listeners[] = $events->attach('replaceList', $dbFlush, -100);
        $this->listeners[] = $events->attach('update', $dbFlush, -100);
    }


    /**
     * @param mixed $id
     * @return mixed|object|ApiProblem
     * @throws NoAccessException
     */
    public function fetch($id) {
        $entity = $this->findEntity($this->entityClassName, $id);
        $this->assertAllowed($entity, __FUNCTION__);

        return $entity;
    }

    /**
     * @param array $params
     * @return Paginator
     * @throws NoAccessException
     */
    public function fetchAll($params = []) {
        $this->assertAllowed($this->entityClassName, __FUNCTION__);

        $collectionClass = $this->getCollectionClass() ?: Paginator::class;

        $list = $this->getRepository()->findAll();
        $list = array_filter($list, function($entity) {
            return $this->isAllowed($entity, Acl::REST_PRIVILEGE_FETCH);
        });

        $adapter = new ArrayAdapter($list);

        return new $collectionClass($adapter);
    }


    /**
     * @param mixed $data
     * @return BaseEntity|ApiProblem
     * @throws NoAccessException
     * @throws ORMException
     */
    public function create($data) {
        $this->assertAllowed($this->entityClassName, __FUNCTION__);

        $entity = $this->createEntity($this->entityClassName);

        $this->getHydrator()->hydrate((array) $data, $entity);
        $this->entityManager->persist($entity);

        return $entity;
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return BaseEntity|ApiProblem
     * @throws NoAccessException
     */
    public function patch($id, $data) {
        $entity = $this->findEntity($this->entityClassName, $id);

        $this->assertAllowed($entity, __FUNCTION__);

        $allData = $this->getHydrator()->extract($entity);
        $data = array_merge($allData, (array) $data);
        $this->getHydrator()->hydrate($data, $entity);

        return $entity;
    }

    /**
     * @param mixed $data
     * @return mixed|ApiProblem
     * @throws NoAccessException
     */
    public function patchList($data) {
        $this->assertAllowed($this->entityClassName, __FUNCTION__);

        return parent::patchList($data);
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return BaseEntity|ApiProblem
     * @throws NoAccessException
     */
    public function update($id, $data) {
        $entity = $this->findEntity($this->entityClassName, $id);

        $this->assertAllowed($entity, __FUNCTION__);
        $this->getHydrator()->hydrate((array)$data, $entity);

        return $entity;
    }

    /**
     * @param mixed $data
     * @return mixed|ApiProblem
     * @throws NoAccessException
     */
    public function replaceList($data) {
        $this->assertAllowed($this->entityClassName, __FUNCTION__);

        return parent::replaceList($data);
    }

    /**
     * @param mixed $id
     * @return bool|null|ApiProblem
     * @throws NoAccessException
     * @throws ORMException
     */
    public function delete($id) {
        $entity = $this->findEntity($this->entityClassName, $id);

        $this->assertAllowed($entity, __FUNCTION__);

        if ($entity !== null) {
            $this->entityManager->remove($entity);
            return true;
        }

        return null;
    }

    /**
     * @param mixed $data
     * @return mixed|ApiProblem
     * @throws NoAccessException
     */
    public function deleteList($data) {
        $this->assertAllowed($this->entityClassName, __FUNCTION__);

        return parent::deleteList($data);
    }

}