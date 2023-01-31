<?php

/**
 * This class copies the concept of Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository with the
 * only change that it extends from Gedmo\Sortable\Entity\Repository\SortableRepository instead of the default
 * Doctrine\ORM\EntityRepository in order to make this work with Sortable Doctrine extension.
 * It also uses the EntityManagerInterface instead of the ObjectManager retrieved from the RegistryManager,
 * because SortableRepository depends on EntityManagerInterface.
 */

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Gedmo\Sortable\Entity\Repository\SortableRepository;

/**
 * Optional EntityRepository base class with a simplified constructor (for autowiring).
 *
 * To use in your class, inject the "em" service and call
 * the parent constructor. For example:
 *
 * class YourEntityRepository extends ServiceEntityRepository
 * {
 *     public function __construct(EntityManagerInterface $em)
 *     {
 *         parent::__construct($em, YourEntity::class);
 *     }
 * }
 */
abstract class SortableServiceEntityRepository extends EntityRepository implements ServiceEntityRepositoryInterface {
    /**
     * @param string $entityClass The class name of the entity this repository manages
     */
    public function __construct(EntityManagerInterface $em, string $entityClass) {
        parent::__construct($em, $em->getClassMetadata($entityClass));
        new SortableRepository($em, $em->getClassMetadata($entityClass));
    }
}
