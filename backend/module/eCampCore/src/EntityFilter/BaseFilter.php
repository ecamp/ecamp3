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
     * @param $alias
     * @param $field
     */
    abstract public function create(QueryBuilder $q, $alias, $field): Expr\Func;

    /**
     * @param $className
     * @param $alias
     * @param $id
     */
    protected function findEntityQueryBuilder($className, $alias, $id): QueryBuilder {
        $q = $this->createQueryBuilder($className, $alias);
        $q->where($alias.'.id = :entityId');
        $q->setParameter('entityId', $id);

        return $q;
    }

    /**
     * @param $className
     * @param string $alias
     */
    protected function findCollectionQueryBuilder($className, $alias): QueryBuilder {
        return $this->createQueryBuilder($className, $alias);
    }
}
