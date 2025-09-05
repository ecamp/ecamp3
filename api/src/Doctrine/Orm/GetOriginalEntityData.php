<?php

namespace App\Doctrine\Orm;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;

readonly class GetOriginalEntityData {
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function getOriginal(object $entity): object {
        $uow = $this->em->getUnitOfWork();
        $changeSet = $uow->getEntityChangeSet($entity);
        $classMetadata = $this->em->getClassMetadata(ClassUtils::getClass($entity));

        $originalEntity = clone $entity;
        $this->em->detach($originalEntity);
        foreach ($changeSet as $key => $value) {
            $classMetadata->setFieldValue($originalEntity, $key, $value[0]);
        }

        return $originalEntity;
    }
}
