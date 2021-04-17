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
use eCamp\Lib\Acl\NotAuthenticatedException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\EntityValidationException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\ApiTools\Rest\ResourceEvent;
use Laminas\Authentication\AuthenticationService;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\Stdlib\RequestInterface;

abstract class AbstractEntityService extends AbstractResourceListener {
    private ServiceUtils $serviceUtils;
    private string $entityClassname;
    private ?string $collectionClassname;
    private string $hydratorClassname;
    private AuthenticationService $authenticationService;
    private EntityRepository $repository;
    private HydratorInterface $hydrator;

    public function __construct(
        ServiceUtils $serviceUtils,
        string $entityClassname,
        ?string $collectionClassname,
        string $hydratorClassname,
        AuthenticationService $authenticationService
    ) {
        $this->serviceUtils = $serviceUtils;
        $this->entityClassname = $entityClassname;
        $this->collectionClassname = $collectionClassname;
        $this->hydratorClassname = $hydratorClassname;
        $this->authenticationService = $authenticationService;

        $this->repository = $serviceUtils->emGetRepository($entityClassname);
        $this->hydrator = $serviceUtils->getHydrator($this->hydratorClassname);

        $this->setEntityClass($this->entityClassname);
        $this->setCollectionClass($this->collectionClassname);
    }

    /**
     * Dispatch an incoming event to the appropriate method
     * Catches exceptions and returns ApiProblem.
     */
    public function dispatch(ResourceEvent $event) {
        try {
            return parent::dispatch($event);
        } catch (NotAuthenticatedException $e) {
            return new ApiProblem(401, $e->getMessage());
        } catch (NoAccessException $e) {
            return new ApiProblem(403, $e->getMessage());
        } catch (EntityNotFoundException $e) {
            return new ApiProblem(404, $e->getMessage());
        } catch (ForeignKeyConstraintViolationException $e) {
            return new ApiProblem(409, $e->getMessage());
        } catch (EntityValidationException $e) {
            return new ApiProblem(422, 'Failed Validation', null, null, ['validation_messages' => $e->getMessages()]);
        }
        /* catch (\Exception $e) {
            return new ApiProblem(500, $e->getMessage());
        }*/
    }

    /**
     * @param string $className
     * @param string $alias
     */
    public function createQueryBuilder($className, $alias): QueryBuilder {
        $q = $this->serviceUtils->emCreateQueryBuilder();
        $q->from($className, $alias)->select($alias);
        $filter = $this->createFilter($q, $className, $alias, 'id');
        if (null != $filter) {
            $q->where($filter);
        }

        return $q;
    }

    /**
     * @throws EntityNotFoundException
     * @throws NonUniqueResultException
     * @throws NoAccessException
     */
    final public function fetch($id): BaseEntity {
        $q = $this->fetchQueryBuilder($id);

        return $this->getQuerySingleResult($q);
    }

    /**
     * @param array $params
     *
     * @throws NoAccessException
     */
    final public function fetchAll($params = []): Paginator {
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
     * @throws NoAccessException
     * @throws ORMException
     */
    final public function create($data): BaseEntity {
        $entity = $this->createEntity($data);
        $this->assertAllowed($entity, __FUNCTION__);
        $this->validateEntity($entity);

        $this->serviceUtils->emPersist($entity);
        $this->serviceUtils->emFlush();

        return $this->createEntityPost($entity, $data);
    }

    /**
     * @param string $id
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NoAccessException
     * @throws EntityNotFoundException
     */
    final public function patch($id, $data): BaseEntity {
        $entity = $this->fetch($id);
        $this->assertAllowed($entity, __FUNCTION__);

        $this->patchEntity($entity, $data);
        $this->validateEntity($entity);
        $this->serviceUtils->emFlush();

        return $entity;
    }

    /**
     * Patches a list of entities.
     *
     * @param \ArrayObject $data Expected in the form
     *                           {
     *                           id: { ***patch paylod*** },
     *                           id2: { ***patch payload*** }
     *                           }
     *
     * @throws EntityNotFoundException
     * @throws EntityValidationException
     * @throws NoAccessException
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @return Paginator
     */
    final public function patchList($data) {
        $result = [];

        /** @var RequestInterface $request */
        $request = $this->getEvent()->getRequest();
        $queryParams = $request->getQuery();

        // validate that the patched entities are all part of the patched collection URI
        $ids = array_keys($data->getArrayCopy());
        $q = $this->fetchAllQueryBuilder($queryParams)
            ->andWhere('row.id IN (:ids)')->setParameter('ids', $ids);
        $numEntities = intval($q->select('count(row.id)')->getQuery()->getSingleScalarResult());
        if ($numEntities !== count($ids)) {
            throw (new EntityValidationException())->setMessages([
                '' => ['invalidIds' => 'Not all of the ids in the payload are part of the patched collection.'],
            ]);
        }

        foreach ($data as $key => $value) {
            $entity = $this->patch($key, $value);
            array_push($result, $entity);
        }

        return $this->fetchAll($queryParams);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws EntityNotFoundException
     * @throws NoAccessException
     */
    final public function update($id, $data): BaseEntity {
        $entity = $this->fetch($id);
        $this->assertAllowed($entity, __FUNCTION__);

        $this->updateEntity($entity, $data);
        $this->validateEntity($entity);
        $this->serviceUtils->emFlush();

        return $entity;
    }

    /**
     * @throws ORMException
     * @throws NoAccessException
     */
    final public function delete($id): bool {
        try {
            $entity = $this->fetch($id);
            $this->assertAllowed($entity, __FUNCTION__);

            $this->deleteEntity($entity);

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
     * @return BaseEntity Instantiated but non-persisted entity
     */
    protected function createEntity($data): BaseEntity {
        $entity = new $this->entityClassname();
        $this->getHydrator()->hydrate((array) $data, $entity);

        return $entity;
    }

    protected function createEntityPost(BaseEntity $entity, $data): BaseEntity {
        return $entity;
    }

    /**
     * @param $data
     */
    protected function patchEntity(BaseEntity $entity, $data): BaseEntity {
        $allData = $this->getHydrator()->extract($entity);
        $data = array_merge($allData, (array) $data);
        $this->getHydrator()->hydrate($data, $entity);

        return $entity;
    }

    /**
     * @param $data
     */
    protected function updateEntity(BaseEntity $entity, $data): BaseEntity {
        $this->getHydrator()->hydrate((array) $data, $entity);

        return $entity;
    }

    protected function deleteEntity(BaseEntity $entity): void {
        $this->serviceUtils->emRemove($entity);
    }

    /**
     * @throws EntityValidationException
     */
    protected function validateEntity(BaseEntity $entity): void {
    }

    protected function getServiceUtils(): ServiceUtils {
        return $this->serviceUtils;
    }

    protected function getRepository(): EntityRepository {
        return $this->repository;
    }

    protected function getHydrator(): HydratorInterface {
        return $this->hydrator;
    }

    /**
     * @return true if User is authenticated
     */
    protected function isAuthenticated(): bool {
        return $this->authenticationService->hasIdentity();
    }

    /**
     * @throws NotAuthenticatedException if no user is authenticated
     */
    protected function assertAuthenticated(): void {
        if (!$this->isAuthenticated()) {
            throw new NotAuthenticatedException();
        }
    }

    /**
     * @return Guest|User
     */
    protected function getAuthUser(): RoleInterface {
        /** @var User $authUser */
        $authUser = new Guest();

        if ($this->isAuthenticated()) {
            $userRepository = $this->serviceUtils->emGetRepository(User::class);
            $userId = $this->authenticationService->getIdentity();
            $user = $userRepository->find($userId);
            if (null != $user) {
                $authUser = $user;
            }
        }

        return $authUser;
    }

    /**
     * @param $resource
     */
    protected function isAllowed($resource, ?string $privilege = null): bool {
        $user = $this->getAuthUser();

        return $this->serviceUtils->aclIsAllowed($user, $resource, $privilege);
    }

    /**
     * @param null $resource
     * @param null $privilege
     *
     * @throws NoAccessException
     */
    protected function assertAllowed($resource, $privilege = null): void {
        $user = $this->getAuthUser();
        $this->serviceUtils->aclAssertAllowed($user, $resource, $privilege);
    }

    /**
     * @param $className
     * @param string $alias
     * @param string $field
     */
    protected function createFilter(QueryBuilder $q, $className, $alias, $field): ?Expr\Func {
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
     */
    protected function findEntityQueryBuilder($className, $alias, $id): QueryBuilder {
        $q = $this->createQueryBuilder($className, $alias);
        $q->andWhere($alias.'.id = :entityId');
        $q->setParameter('entityId', $id);

        return $q;
    }

    /**
     * @param $className
     * @param string $alias
     * @param array  $params
     */
    protected function findCollectionQueryBuilder($className, $alias, $params): QueryBuilder {
        return $this->createQueryBuilder($className, $alias);
    }

    /**
     * @throws EntityNotFoundException
     * @throws NoAccessException
     * @throws NonUniqueResultException
     */
    protected function getQuerySingleResult(QueryBuilder $q): BaseEntity {
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

    protected function fetchQueryBuilder($id): QueryBuilder {
        return $this->findEntityQueryBuilder($this->entityClassname, 'row', $id);
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
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
     */
    protected function findEntity($className, $id): BaseEntity {
        $q = $this->findEntityQueryBuilder($className, 'row', $id);

        try {
            $entity = $this->getQuerySingleResult($q);
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException("Entity {$className} with id {$id} not found", 0, $e);
        }

        return $entity;
    }

    /**
     * @throws EntityValidationException
     *
     * @return mixed
     */
    protected function findRelatedEntity(string $className, $data, string $key): BaseEntity {
        // check if foreign key exists
        if (empty($data->{$key})) {
            throw (new EntityValidationException())->setMessages([$key => ['isEmpty' => "Value is required and can't be empty"]]);
        }

        // try to find Entity
        try {
            $entity = $this->findEntity($className, $data->{$key});
        } catch (EntityNotFoundException $e) {
            throw (new EntityValidationException())->setMessages([$key => ['notFound' => "Entity {$className} with id {$data->{$key}} not found or not accessible"]]);
        }

        return $entity;
    }
}
