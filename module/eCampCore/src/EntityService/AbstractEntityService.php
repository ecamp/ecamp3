<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\User;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\ServiceManager\AclAware;
use eCamp\Lib\ServiceManager\EntityFilterManager;
use eCamp\Lib\ServiceManager\EntityFilterManagerAware;
use eCamp\Lib\ServiceManager\EntityManagerAware;
use eCamp\Lib\ServiceManager\HydratorPluginManagerAware;
use Zend\Authentication\AuthenticationService;
use Zend\Hydrator\HydratorInterface;
use Zend\Hydrator\HydratorPluginManager;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

abstract class AbstractEntityService extends AbstractResourceListener
    implements EntityManagerAware, EntityFilterManagerAware, HydratorPluginManagerAware, AclAware {
    /** @var string */
    private $entityClassname;

    /** @var EntityManager */
    protected $entityManager;

    /** @var EntityRepository */
    private $repository;

    /** @var EntityFilterManager */
    protected $entityFilterManager;

    /** @var string */
    private $hydratorClassname;

    /** @var HydratorPluginManager */
    protected $hydratorPluginManager;

    /** @var HydratorInterface */
    private $hydrator;


    /** @var Acl */
    protected $acl;


    public function __construct($entityClassname, $hydratorClassname) {
        $this->entityClassname = $entityClassname;
        $this->hydratorClassname = $hydratorClassname;
    }



    public function setEntityManager(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    protected function getEntityManager() {
        return $this->entityManager;
    }

    /** @return EntityRepository */
    protected function getRepository() {
        if ($this->repository == null) {
            $this->repository = $this->getEntityManager()->getRepository($this->entityClassname);
        }
        return $this->repository;
    }

    /**
     * @param BaseEntity $entity
     * @return array
     */
    protected function getOrigEntityData($entity) {
        $uow = $this->entityManager->getUnitOfWork();
        return $uow->getOriginalEntityData($entity);
    }



    /** @return EntityFilterManager */
    protected function getEntityFilterManager() {
        return $this->entityFilterManager;
    }
    public function setEntityFilterManager(EntityFilterManager $entityFilterManager) {
        $this->entityFilterManager = $entityFilterManager;
    }



    public function setHydratorPluginManager(HydratorPluginManager $hydratorPluginManager) {
        $this->hydratorPluginManager = $hydratorPluginManager;
    }

    /** @return HydratorInterface */
    protected function getHydrator() {
        if ($this->hydrator == null) {
            $this->hydrator = $this->hydratorPluginManager->get($this->hydratorClassname);
        }
        return $this->hydrator;
    }



    public function setAcl(Acl $acl) {
        $this->acl = $acl;
    }

    protected function getAcl() {
        return $this->acl;
    }

    /**
     * @return null|User
     */
    protected function getAuthUser() {
        /** @var User $user */
        $user = null;

        $authService = new AuthenticationService();
        if ($authService->hasIdentity()) {
            $userRepository = $this->getEntityManager()->getRepository(User::class);
            $userId = $authService->getIdentity();
            $user = $userRepository->find($userId);
        }

        return $user;
    }

    /**
     * @param null $resource
     * @param null $privilege
     * @return bool
     */
    protected function isAllowed($resource, $privilege = null) {
        $user = $this->getAuthUser();
        return $this->getAcl()->isAllowed($user, $resource, $privilege);
    }

    /**
     * @param null $resource
     * @param null $privilege
     * @throws \eCamp\Lib\Acl\NoAccessException
     */
    protected function assertAllowed($resource, $privilege = null) {
        $user = $this->getAuthUser();
        return $this->getAcl()->assertAllowed($user, $resource, $privilege);
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
     * @param string $alias
     * @return QueryBuilder
     */
    public function createQueryBuilder($className, $alias) {
        $q = $this->entityManager->createQueryBuilder();
        $q->from($className, $alias)->select($alias);
        $filter = $this->createFilter($q, $className, $alias, 'id');
        if ($filter != null) {
            $q->where($filter);
        }
        return $q;
    }

    /**
     * @param QueryBuilder $q
     * @param $className
     * @param string $alias
     * @param string $field
     * @return Expr\Func
     */
    protected function createFilter(QueryBuilder $q, $className, $alias, $field) {
        $filter = $this->entityFilterManager->getByEntityClass($className);
        if ($filter == null) {
            return null;
        }
        return $filter->create($q, $alias, $field);
    }

    /**
     * @param $className
     * @param $alias
     * @param $id
     * @return QueryBuilder
     */
    protected function findEntityQueryBuilder($className, $alias, $id) {
        $q = $this->createQueryBuilder($className, $alias);
        $q->andWhere($alias . '.id = :entity_id');
        $q->setParameter('entity_id', $id);
        return $q;
    }

    /**
     * @param $className
     * @param string $alias
     * @return QueryBuilder
     */
    protected function findCollectionQueryBuilder($className, $alias) {
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
            return null;
        } catch (\Exception $ex) {
            return new ApiProblem(500, $ex->getMessage());
        }
    }

    protected function getQueryResult(QueryBuilder $q) {
        try {
            $rows = $q->getQuery()->getResult();
            $rows = array_filter($rows, function ($entity) {
                return $this->isAllowed($entity, Acl::REST_PRIVILEGE_FETCH);
            });
            return $rows;
        } catch (\Exception $ex) {
            return new ApiProblem(500, $ex->getMessage());
        }
    }

    protected function fetchQueryBuilder($id) {
        $q =  $this->findEntityQueryBuilder($this->entityClassname, 'row', $id);
        return $q;
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = $this->findCollectionQueryBuilder($this->entityClassname, 'row');
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
        $entity = $this->getQuerySingleResult($q);
        return $entity;
    }

    /**
     * @param mixed $id
     * @return BaseEntity|ApiProblem
     */
    public function fetch($id) {
        $q = $this->fetchQueryBuilder($id);
        $entity = $this->getQuerySingleResult($q);
        return $entity;
    }

    /**
     * @param array $params
     * @return Paginator
     * @throws NoAccessException
     */
    public function fetchAll($params = []) {
        $this->assertAllowed($this->entityClassname, __FUNCTION__);
        $q = $this->fetchAllQueryBuilder($params);
        $list = $this->getQueryResult($q);
        if ($list instanceof ApiProblem) {
            return $list;
        }
        $collectionClass = $this->getCollectionClass() ?: Paginator::class;
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
        $this->assertAllowed($this->entityClassname, __FUNCTION__);
        $entity = $this->createEntity($this->entityClassname);
        if ($entity instanceof ApiProblem) {
            return $entity;
        }
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
        $q = $this->fetchQueryBuilder($id);
        $entity = $this->getQuerySingleResult($q);
        if ($entity instanceof ApiProblem) {
            return $entity;
        }
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
        $this->assertAllowed($this->entityClassname, __FUNCTION__);
        return parent::patchList($data);
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return BaseEntity|ApiProblem
     * @throws NoAccessException
     */
    public function update($id, $data) {
        $q = $this->fetchQueryBuilder($id);
        $entity = $this->getQuerySingleResult($q);
        if ($entity instanceof ApiProblem) {
            return $entity;
        }
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
        $this->assertAllowed($this->entityClassname, __FUNCTION__);
        return parent::replaceList($data);
    }

    /**
     * @param mixed $id
     * @return bool|null|ApiProblem
     * @throws NoAccessException
     * @throws ORMException
     */
    public function delete($id) {
        $q = $this->fetchQueryBuilder($id);
        $entity = $this->getQuerySingleResult($q);
        if ($entity instanceof ApiProblem) {
            return $entity;
        }
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
        $this->assertAllowed($this->entityClassname, __FUNCTION__);
        return parent::deleteList($data);
    }
}
