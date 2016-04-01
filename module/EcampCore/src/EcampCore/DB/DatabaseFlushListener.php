<?php

namespace EcampCore\DB;

use Doctrine\ORM\EntityManager;
use EcampLib\DB\AbstractDatabaseFlushListener;

class DatabaseFlushListener extends AbstractDatabaseFlushListener
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }


    public function flush()
    {
        $this->entityManager->flush();
    }

    public function rollback()
    {
        if($this->entityManager->getConnection()->isTransactionActive()){
            $this->entityManager->getConnection()->rollback();
        }
    }
}