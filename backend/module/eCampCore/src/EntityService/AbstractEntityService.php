<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\User;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;
use Exception;
use Zend\Authentication\AuthenticationService;
use Zend\Hydrator\HydratorInterface;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;
use ZF\Rest\ResourceEvent;

abstract class AbstractEntityService extends AbstractResourceListener {
    /** @var ServiceUtils */
    private $serviceUtils;

    /** @var string */
    private $entityClassname;

    /** @var string */
    private $hydratorClassname;

    /** @var AuthenticationService */
    private $authenticationService;

    /** @var EntityRepository */
    private $repository;

    /** @var HydratorInterface */
    private $hydrator;

    public function __construct(
        ServiceUtils $serviceUtils,
        string $entityClassname,
        string $hydratorClassname,
        AuthenticationService $authenticationService
    ) {
        $this->serviceUtils = $serviceUtils;
        $this->entityClassname = $entityClassname;
        $this->hydratorClassname = $hydratorClassname;
        $this->authenticationService = $authenticationService;
    }

    /**
     * Dispatch an incoming event to the appropriate method
     * Catches exceptions and returns ApiProblem.
     *
     * @return mixed
     */
    public function dispatch(ResourceEvent $event) {
        try {
            return parent::dispatch($event);
        } catch (NoAccessException $e) {
            return new ApiProblem(403, $e->getMessage());
        } catch (EntityNotFoundException $ex) {
            return new ApiProblem(404, $ex->getMessage());
        } catch (Exception $e) {
            return new ApiProblem(500, $e->getMessage());
        }
    }

    /**
     * @param string $className
     * @param string $alias
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder($className, $alias) {
        $q = $this->serviceUtils->emCreateQueryBuilder();
        $q->from($className, $alias)->select($alias);
        $filter = $this->createFilter($q, $className, $alias, 'id');
        if (null != $filter) {
            $q->where($filter);
        }

        return $q;
    }

    /**
     * @param mixed $id
     *
     * @return ApiProblem|BaseEntity
     */
    public function fetch($id) {
        $q = $this->fetchQueryBuilder($id);

        return $this->getQuerySingleResult($q);
    }

    /**
     * @param array $params
     *
     * @throws NoAccessException
     *
     * @return ApiProblem|array
     */
    public function fetchAll($params = []) {
        $this->assertAllowed($this->entityClassname, __FUNCTION__);
        $q = $this->fetchAllQueryBuilder($params);
        $list = $this->getQueryResult($q);

        $collectionClass = $this->getCollectionClass() ?: Paginator::class;
        $adapter = new ArrayAdapter($list);

        return new $collectionClass($adapter);
    }

    /**
     * @param mixed $data
     *
     * @throws NoAccessException
     * @throws ORMException
     *
     * @return ApiProblem|BaseEntity
     */
    public function create($data) {
        $this->assertAllowed($this->entityClassname, __FUNCTION__);
        $entity = $this->createWithoutPersist($data);
        $this->serviceUtils->emPersist($entity);

        return $entity;
    }

    /**
     * @param mixed $data
     *
     * @throws NoAccessException
     * @throws ORMException
     *
     * @return ApiProblem|BaseEntity
     */
    public function createWithoutPersist($data) {
        // $this->assertAllowed($this->entityClassname, __FUNCTION__); // should this be protected by ACL?
        $entity = $this->createEntity($this->entityClassname);
        $this->getHydrator()->hydrate((array) $data, $entity);

        return $entity;
    }

    /**
     * @param mixed $id
     * @param mixed $data
     *
     * @throws NoAccessException
     *
     * @return ApiProblem|BaseEntity
     */
    public function patch($id, $data) {
        $q = $this->fetchQueryBuilder($id);
        $entity = $this->getQuerySingleResult($q);
        $this->assertAllowed($entity, __FUNCTION__);
        $allData = $this->getHydrator()->extract($entity);
        $data = array_merge($allData, (array) $data);
        $this->getHydrator()->hydrate($data, $entity);

        return $entity;
    }

    /**
     * @param mixed $data
     *
     * @throws NoAccessException
     *
     * @return ApiProblem|mixed
     */
    public function patchList($data) {
        $this->assertAllowed($this->entityClassname, __FUNCTION__);

        return parent::patchList($data);
    }

    /**
     * @param mixed $id
     * @param mixed $data
     *
     * @throws NoAccessException
     *
     * @return ApiProblem|BaseEntity
     */
    public function update($id, $data) {
        $q = $this->fetchQueryBuilder($id);
        $entity = $this->getQuerySingleResult($q);
        $this->assertAllowed($entity, __FUNCTION__);
        $this->getHydrator()->hydrate((array) $data, $entity);

        return $entity;
    }

    /**
     * @param mixed $data
     *
     * @throws NoAccessException
     *
     * @return ApiProblem|mixed
     */
    public function replaceList($data) {
        $this->assertAllowed($this->entityClassname, __FUNCTION__);

        return parent::replaceList($data);
    }

    /**
     * @param mixed $id
     *
     * @throws NoAccessException
     * @throws ORMException
     *
     * @return null|ApiProblem|bool
     */
    public function delete($id) {
        $q = $this->fetchQueryBuilder($id);
        $entity = $this->getQuerySingleResult($q);
        $this->assertAllowed($entity, __FUNCTION__);
        if (null !== $entity) {
            $this->serviceUtils->emRemove($entity);

            return true;
        }

        return null;
    }

    /**
     * @param mixed $data
     *
     * @throws NoAccessException
     *
     * @return ApiProblem|mixed
     */
    public function deleteList($data) {
        $this->assertAllowed($this->entityClassname, __FUNCTION__);

        return parent::deleteList($data);
    }

    /**
     * @return ServiceUtils
     */
    protected function getServiceUtils() {
        return $this->serviceUtils;
    }

    /** @return EntityRepository */
    protected function getRepository() {
        if (null == $this->repository) {
            $this->repository = $this->serviceUtils->emGetRepository($this->entityClassname);
        }

        return $this->repository;
    }

    /** @return HydratorInterface */
    protected function getHydrator() {
        if (null == $this->hydrator) {
            $this->hydrator = $this->serviceUtils->getHydrator($this->hydratorClassname);
        }

        return $this->hydrator;
    }

    /**
     * @return null|User
     */
    protected function getAuthUser() {
        /** @var User $user */
        $user = null;

        if ($this->authenticationService->hasIdentity()) {
            $userRepository = $this->serviceUtils->emGetRepository(User::class);
            $userId = $this->authenticationService->getIdentity();
            $user = $userRepository->find($userId);
        }

        return $user;
    }

    /**
     * @param null $resource
     * @param null $privilege
     *
     * @return bool
     */
    protected function isAllowed($resource, $privilege = null) {
        $user = $this->getAuthUser();

        return $this->serviceUtils->aclIsAllowed($user, $resource, $privilege);
    }

    /**
     * @param null $resource
     * @param null $privilege
     *
     * @throws NoAccessException
     */
    protected function assertAllowed($resource, $privilege = null) {
        $user = $this->getAuthUser();
        $this->serviceUtils->aclAssertAllowed($user, $resource, $privilege);
    }

    /**
     * @param string $className
     *
     * @return ApiProblem|BaseEntity
     */
    protected function createEntity($className) {
        return new $className();
    }

    /**
     * @param $className
     * @param string $alias
     * @param string $field
     *
     * @return Expr\Func
     */
    protected function createFilter(QueryBuilder $q, $className, $alias, $field) {
        $filter = $this->serviceUtils->getEntityFilter($className);
        if (null == $filter) {
            return null;
        }

        return $filter->create($q, $alias, $field);
    }

    /**
     * @param $className
     * @param $alias
     * @param $id
     *
     * @return QueryBuilder
     */
    protected function findEntityQueryBuilder($className, $alias, $id) {
        $q = $this->createQueryBuilder($className, $alias);
        $q->andWhere($alias.'.id = :entity_id');
        $q->setParameter('entity_id', $id);

        return $q;
    }

    /**
     * @param $className
     * @param string $alias
     * @param array  $params
     *
     * @return QueryBuilder
     */
    protected function findCollectionQueryBuilder($className, $alias, $params) {
        return $this->createQueryBuilder($className, $alias);
    }

    protected function getQuerySingleResult(QueryBuilder $q) {
        try {
            $row = $q->getQuery()->getSingleResult();
            if ($this->isAllowed($row, Acl::REST_PRIVILEGE_FETCH)) {
                return $row;
            }

            return null;
        } catch (NoResultException $ex) {
            throw new EntityNotFoundException('Entity not found', 0, $ex);
        }
    }

    /**
     * @throws NoAccessException
     *
     * @return ApiProblem|array
     */
    protected function getQueryResult(QueryBuilder $q) {
        $this->assertAllowed($this->entityClassname, Acl::REST_PRIVILEGE_FETCH_ALL);

        $rows = $q->getQuery()->getResult();
        $rows = array_filter($rows, function ($entity) {
            return $this->isAllowed($entity, Acl::REST_PRIVILEGE_FETCH);
        });

        return $rows;
    }

    protected function fetchQueryBuilder($id) {
        return $this->findEntityQueryBuilder($this->entityClassname, 'row', $id);
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = $this->findCollectionQueryBuilder($this->entityClassname, 'row', $params);
        if (isset($params['where'])) {
            $q->andWhere($params['where']);
        }
        if (isset($params['order_by'])) {
            $q->orderBy($params['order_by']);
        }

        return $q;
    }

    protected function findEntity($className, $id) {
        $q = $this->findEntityQueryBuilder($className, 'row', $id);

        try {
            $entity = $this->getQuerySingleResult($q);
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException("Entity {$className} with id {$id} not found", 0, $e);
        }

        return $entity;
    }
}
