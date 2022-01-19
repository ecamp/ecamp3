<?php

/**
 * This class is a 1:1 copy of Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository with the only change that it
 * extends from Gedmo\Sortable\Entity\Repository\SortableRepository instead of the default Doctrine\ORM\EntityRepository in
 * order to make this work with Sortable Doctrine extension.
 */

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Sortable\Entity\Repository\SortableRepository;
use LogicException;
use function sprintf;

/**
 * Optional EntityRepository base class with a simplified constructor (for autowiring).
 *
 * To use in your class, inject the "registry" service and call
 * the parent constructor. For example:
 *
 * class YourEntityRepository extends ServiceEntityRepository
 * {
 *     public function __construct(ManagerRegistry $registry)
 *     {
 *         parent::__construct($registry, YourEntity::class);
 *     }
 * }
 *
 * @template T
 * @template-extends EntityRepository<T>
 */
abstract class SortableServiceEntityRepository extends SortableRepository implements ServiceEntityRepositoryInterface {
    /**
     * @param string $entityClass The class name of the entity this repository manages
     * @psalm-param class-string<T> $entityClass
     */
    public function __construct(ManagerRegistry $registry, string $entityClass) {
        $manager = $registry->getManagerForClass($entityClass);

        if (null === $manager) {
            throw new LogicException(sprintf(
                'Could not find the entity manager for class "%s". Check your Doctrine configuration to make sure it is configured to load this entity’s metadata.',
                $entityClass
            ));
        }

        parent::__construct($manager, $manager->getClassMetadata($entityClass));
    }
}
