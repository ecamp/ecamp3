<?php
//
//namespace eCamp\Lib\Service;
//
//use Doctrine\ORM\EntityRepository;
//use Doctrine\ORM\Query\Expr;
//use Doctrine\ORM\QueryBuilder;
//use Zend\Hydrator\HydratorInterface;
//use ZF\Rest\AbstractResourceListener;
//
//abstract class AbstractEntityService extends AbstractResourceListener
//{
//    use AbstractServiceTrait;
//
//
//    /** @var string */
//    private $entityClassName;
//
//    /** @var HydratorInterface */
//    private $hydrator;
//
//    /** @var EntityRepository */
//    private $repository;
//
//
//    public function __construct()
//    {
//    }
//
////    public function __construct(
////        HydratorInterface $hydrator,
////        $entityClassName
////    ) {
////        $this->entityClassName = $entityClassName;
////        $this->hydrator = $hydrator;
////    }
//
//
//
//    /** @return EntityRepository */
//    protected function getRepository()
//    {
//        if ($this->repository == null) {
//            $this->repository = $this->entityManager->getRepository($this->entityClassName);
//        }
//        return $this->repository;
//    }
//
//    /** @return HydratorInterface */
//    protected function getHydrator()
//    {
//        return $this->hydrator;
//    }
//
//
//
//    /**
//     * @param string $className
//     * @param string $alias
//     * @return QueryBuilder
//     */
//    public function createQueryBuilder($className, $alias)
//    {
//        $q = $this->entityManager->createQueryBuilder();
//        $q->from($className, $alias)->select($alias);
//
//        $filter = $this->createFilter($q, $className, $alias, 'id');
//        if ($filter != null) {
//            $q->where($filter);
//        }
//
//        return $q;
//    }
//
//    /**
//     * @param QueryBuilder $q
//     * @param $className
//     * @param string $alias
//     * @param string $field
//     * @return Expr\Func
//     */
//    protected function createFilter(QueryBuilder $q, $className, $alias, $field)
//    {
//        $filter = $this->entityFilterManager->getByEntityClass($className);
//        if ($filter == null) {
//            return null;
//        }
//
//        return $filter->create($q, $alias, $field);
//    }
//
//    /**
//     * @param $className
//     * @param $alias
//     * @param $id
//     * @return QueryBuilder
//     */
//    protected function findEntityQueryBuilder($className, $alias, $id)
//    {
//        $q = $this->createQueryBuilder($className, $alias);
//        $q->andWhere($alias . '.id = :entity_id');
//        $q->setParameter('entity_id', $id);
//        return $q;
//    }
//
//    /**
//     * @param $className
//     * @param string $alias
//     * @return QueryBuilder
//     */
//    protected function findCollectionQueryBuilder($className, $alias)
//    {
//        return $this->createQueryBuilder($className, $alias);
//    }
//
//
//    protected function fetchQueryBuilder($id)
//    {
//        $q =  $this->findEntityQueryBuilder($this->entityClassName, 'row', $id);
//
//        return $q;
//    }
//
//    protected function fetchAllQueryBuilder($params = [])
//    {
//        $q = $this->findCollectionQueryBuilder($this->entityClassName, 'row');
//        if (isset($params['where'])) {
//            $q->andWhere($params['where']);
//        }
//        if (isset($params['order_by'])) {
//            $q->orderBy($params['order_by']);
//        }
//
//        return $q;
//    }
//
//
//    /**
//     * @param mixed $id
//     * @return BaseEntity|ApiProblem
//     */
//    public function fetch($id)
//    {
//        $q = $this->fetchQueryBuilder($id);
//        $entity = $this->getQuerySingleResult($q);
//
//        return $entity;
//    }
//
//}
