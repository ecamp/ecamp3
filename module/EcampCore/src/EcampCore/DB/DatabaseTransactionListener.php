<?php

namespace EcampCore\DB;

use Doctrine\ORM\EntityManager;
use EcampLib\DB\AbstractDatabaseTransactionListener;

class DatabaseTransactionListener extends AbstractDatabaseTransactionListener
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** @return \Doctrine\DBAL\Connection */
    private function getConnection()
    {
        return $this->entityManager->getConnection();
    }

    public function beginTransaction()
    {
        $this->getConnection()->beginTransaction();
    }

    public function commitTransaction()
    {
        if ($this->getConnection()->isTransactionActive()) {
            if (! $this->getConnection()->isRollbackOnly()) {
                $this->getConnection()->commit();
            } else {
                $this->getConnection()->rollBack();
            }
        }
    }

    public function rollbackTransaction()
    {
        if ($this->getConnection()->isTransactionActive()) {
            $this->getConnection()->rollBack();
        }
    }
}
