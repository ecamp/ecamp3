<?php

namespace eCamp\Core\EntityFilter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Auth\AuthUserProvider;
use eCamp\Core\ServiceManager\AuthUserProviderAware;
use eCamp\Lib\EntityFilter\EntityFilterInterface;
use eCamp\Lib\ServiceManager\EntityManagerAware;

abstract class BaseFilter implements EntityFilterInterface, AuthUserProviderAware, EntityManagerAware
{
    /** @var AuthUserProvider */
    protected $authUserProvider;

    public function setAuthUserProvider(AuthUserProvider $authUserProvider)
    {
        $this->authUserProvider = $authUserProvider;
    }


    /** @var EntityManager */
    protected $entityManager;

    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function createQueryBuilder($className, $alias)
    {
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
    protected function findEntityQueryBuilder($className, $alias, $id)
    {
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
    protected function findCollectionQueryBuilder($className, $alias)
    {
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
