<?php

namespace eCamp\Core\EntityService;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\User;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\Guest;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;
use Exception;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\ApiTools\Rest\ResourceEvent;
use Laminas\Authentication\AuthenticationService;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator;

abstract class AbstractEntityService extends AbstractResourceListener {
    private ServiceUtils $serviceUtils;

    private string $entityClassname;

    private string $hydratorClassname;

    private AuthenticationService $authenticationService;

    private EntityRepository $repository;

    private HydratorInterface $hydrator;

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

        $this->repository = $serviceUtils->emGetRepository($entityClassname);
        $this->hydrator = $serviceUtils->getHydrator($hydratorClassname);
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
        } catch (EntityNotFoundException $e) {
            return new ApiProblem(404, $e->getMessage());
        } catch (ForeignKeyConstraintViolationException $e) {
            return new ApiProblem(409, $e->getMessage());
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
     * @throws EntityNotFoundException
     * @throws NonUniqueResultException
     * @throws NoAccessException
     *
     * @return BaseEntity
     */
    final public function fetch($id) {
        $q = $this->fetchQueryBuilder($id);

        return $this->getQuerySingleResult($q);
    }

    /**
     * @param array $params
     *
     * @throws NoAccessException
     *
     * @return ApiProblem|Paginator
     */
    final public function fetchAll($params = []) {
        $this->assertAllowed($this->entityClassname, __FUNCTION__);
        $q = $this->fetchAllQueryBuilder($params);
        $list = $this->getQueryResult($q);

        $collectionClass = $this->getCollectionClass() ?: Paginator::class;
        $adapter = new ArrayAdapter($list);

        return new $collectionClass($adapter);
    }

    /**
     * Calls createEntity + is responsible for permission check and persistance.
     *
     * @param mixed $data
     *
     * @throws NoAccessException
     * @throws ORMException
     *
     * @return BaseEntity
     */
    final public function create($data) {
        $entity = $this->createEntity($data);
        $this->assertAllowed($entity, __FUNCTION__);

        $this->serviceUtils->emPersist($entity);
        $this->serviceUtils->emFlush();

        return $this->createEntityPost($entity, $data);
    }

    /**
     * @param string $id
     * @param mixed  $data
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NoAccessException
     * @throws EntityNotFoundException
     *
     * @return BaseEntity
     */
    final public function patch($id, $data) {
        $entity = $this->fetch($id);
        $this->assertAllowed($entity, __FUNCTION__);

        $this->patchEntity($entity, $data);
        $this->serviceUtils->emFlush();

        return $entity;
    }

    /**
     * @param mixed $id
     * @param mixed $data
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws EntityNotFoundException
     * @throws NoAccessException
     *
     * @return BaseEntity
     */
    final public function update($id, $data) {
        $entity = $this->fetch($id);
        $this->assertAllowed($entity, __FUNCTION__);

        $this->updateEntity($entity, $data);
        $this->serviceUtils->emFlush();

        return $entity;
    }

    /**
     * @param mixed $id
     *
     * @throws ORMException
     * @throws NoAccessException
     *
     * @return null|ApiProblem|bool
     */
    final public function delete($id) {
        try {
            $entity = $this->fetch($id);
            $this->assertAllowed($entity, __FUNCTION__);

            $this->deleteEntity($entity);
            $this->serviceUtils->emRemove($entity);
            $this->serviceUtils->emFlush();

            return true;
        } catch (EntityNotFoundException $ex) {
            // Entity not in Database.
            return true;
        }
    }

    /**
     * Responsible for creation of entity + hydration of data.
     * Should be overriden by subclass for specific additional business logic during create process.
     *
     * @param mixed $data
     *
     * @return BaseEntity Instantiated but non-persisted entity
     */
    protected function createEntity($data) {
        $entity = new $this->entityClassname();
        $this->getHydrator()->hydrate((array) $data, $entity);

        return $entity;
    }

    /**
     * @param mixed $data
     *
     * @return BaseEntity
     */
    protected function createEntityPost(BaseEntity $entity, $data) {
        return $entity;
    }

    /**
     * @param $entity
     * @param $data
     *
     * @return BaseEntity
     */
    protected function patchEntity(BaseEntity $entity, $data) {
        $allData = $this->getHydrator()->extract($entity);
        $data = array_merge($allData, (array) $data);
        $this->getHydrator()->hydrate($data, $entity);

        return $entity;
    }

    /**
     * @param $entity
     * @param $data
     *
     * @return BaseEntity
     */
    protected function updateEntity(BaseEntity $entity, $data) {
        $this->getHydrator()->hydrate((array) $data, $entity);

        return $entity;
    }

    /**
     * @param $entity
     *
     * @return BaseEntity
     */
    protected function deleteEntity(BaseEntity $entity) {
        return $entity;
    }

    /**
     * @return ServiceUtils
     */
    protected function getServiceUtils() {
        return $this->serviceUtils;
    }

    /**
     * @return EntityRepository
     */
    protected function getRepository() {
        return $this->repository;
    }

    /**
     * @return HydratorInterface
     */
    protected function getHydrator() {
        return $this->hydrator;
    }

    /**
     * @return Guest|User
     */
    protected function getAuthUser() {
        /** @var User $user */
        $user = new Guest();

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
        $q->andWhere($alias.'.id = :entityId');
        $q->setParameter('entityId', $id);

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

    /**
     * @throws EntityNotFoundException
     * @throws NoAccessException
     * @throws NonUniqueResultException
     *
     * @return mixed
     */
    protected function getQuerySingleResult(QueryBuilder $q) {
        try {
            $row = $q->getQuery()->getSingleResult();
            $this->assertAllowed($row, Acl::REST_PRIVILEGE_FETCH);

            return $row;
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

        return array_filter($rows, function ($entity) {
            return $this->isAllowed($entity, Acl::REST_PRIVILEGE_FETCH);
        });
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

    /**
     * @param $className
     * @param $id
     *
     * @throws EntityNotFoundException
     * @throws NoAccessException
     * @throws NonUniqueResultException
     *
     * @return mixed
     */
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
