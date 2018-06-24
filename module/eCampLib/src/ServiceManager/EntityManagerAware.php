<?php

namespace eCamp\Lib\ServiceManager;

use Doctrine\ORM\EntityManager;

interface EntityManagerAware {
    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager);
}
