<?php

namespace eCamp\Core\EntityFilter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Auth\AuthUserProvider;
use eCamp\Lib\EntityFilter\EntityFilterInterface;

abstract class BaseFilter implements EntityFilterInterface {

    /** @var AuthUserProvider */
    protected $authUserProvider;

    /** @var EntityManager */
    protected $entityManager;


    public function __construct(AuthUserProvider $authUserProvider, EntityManager $entityManager) {
        $this->authUserProvider = $authUserProvider;
        $this->entityManager = $entityManager;
    }


    public function createQueryBuilder($className, $alias) {
        $q = $this->entityManager->createQueryBuilder();
        $q->from($className, $alias)->select($alias);
        return $q;
    }

    /**
     * @param $className
     * @param $alias
     * @param $id
     * @return QueryBuilder
     */
    protected function findEntityQueryBuilder($className, $alias, $id) {
        $q = $this->createQueryBuilder($className, $alias);
        $q->where($alias . '.id = :entity_id');
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

    /**
     * @param QueryBuilder $q
     * @param $alias
     * @param $field
     * @return Expr\Func
     */
    abstract public function create(QueryBuilder $q, $alias, $field);
}
