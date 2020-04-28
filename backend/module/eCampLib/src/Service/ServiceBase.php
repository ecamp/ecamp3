<?php

//
//namespace eCamp\Lib\Service;
//
//use Doctrine\ORM\EntityManager;
//use eCamp\Lib\Acl\Acl;
//use eCamp\Lib\Entity\BaseEntity;
//use eCamp\Lib\ServiceManager\EntityFilterManager;
//
//class ServiceBase
//{
//
//    /** @var Acl */
//    private $acl;
//
//    /** @var EntityManager */
//    private $entityManager;
//
//
//    public function __construct(Acl $acl, EntityManager $entityManager) {
//        $this->acl = $acl;
//        $this->entityManager = $entityManager;
//    }
//
//
//    public function getAcl() {
//        return $this->acl;
//    }
//
//    public function getEntityManager() {
//        return $this->entityManager;
//    }
//
//
//    /**
//     * @param BaseEntity $entity
//     * @return array
//     */
//    protected function getOrigEntityData($entity)
//    {
//        $uow = $this->entityManager->getUnitOfWork();
//        return $uow->getOriginalEntityData($entity);
//    }
//
//}
