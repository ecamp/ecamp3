<?php

/*
 * This file was originally copied from api-platform/core, but modified to implement our own cache tag logic.
 *
 * For original author and license information see upstream file.
 *
 * Upstream file (main branch):               https://github.com/api-platform/core/blob/main/src/Doctrine/EventListener/PurgeHttpCacheListener.php
 * Upstream file (last synchronized version): https://github.com/api-platform/core/blob/d8e2d0c5e9b48c15d60a734086b0102b6ecf95c8/src/Doctrine/EventListener/PurgeHttpCacheListener.php
 * Last synchronized commit:                  2024-03-05 / d8e2d0c5e9b48c15d60a734086b0102b6ecf95c8
 */

declare(strict_types=1);

namespace App\HttpCache;

use ApiPlatform\Api\IriConverterInterface as LegacyIriConverterInterface;
use ApiPlatform\Api\ResourceClassResolverInterface as LegacyResourceClassResolverInterface;
use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Exception\RuntimeException;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\ResourceClassResolverInterface;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use ApiPlatform\Metadata\Util\ClassInfoTrait;
use App\Entity\BaseEntity;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\AssociationMapping;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\PersistentCollection;
use FOS\HttpCacheBundle\CacheManager;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Purges responses containing modified entities from the proxy cache.
 */
final class PurgeHttpCacheListener {
    use ClassInfoTrait;

    public const IRI_RELATION_DELIMITER = '#';

    public function __construct(private readonly IriConverterInterface|LegacyIriConverterInterface $iriConverter, private readonly LegacyResourceClassResolverInterface|ResourceClassResolverInterface $resourceClassResolver, private readonly PropertyAccessorInterface $propertyAccessor, private readonly ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory, private readonly CacheManager $cacheManager) {}

    /**
     * Collects tags from the previous and the current version of the updated entities to purge related documents.
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs): void {
        $changeSet = $eventArgs->getEntityChangeSet();
        $objectManager = $eventArgs->getObjectManager();
        $associationMappings = $objectManager->getClassMetadata(ClassUtils::getClass($eventArgs->getObject()))->getAssociationMappings();

        foreach ($changeSet as $key => $value) {
            if (!isset($associationMappings[$key])) {
                continue;
            }
            $mappings = $associationMappings[$key];
            $relatedProperty = $mappings['isOwningSide'] ? $mappings['inversedBy'] : $mappings['mappedBy'];
            if (!$relatedProperty) {
                continue;
            }

            $this->addTagsFor($value[0], $relatedProperty);
            $this->addTagsFor($value[1], $relatedProperty);
        }
    }

    /**
     * Collects tags from inserted, updated and deleted entities, including relations.
     */
    public function onFlush(OnFlushEventArgs $eventArgs): void {
        /** @var EntityManagerInterface */
        $em = $eventArgs->getObjectManager();

        if (!$em instanceof EntityManagerInterface) {
            return;
        }

        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $this->gatherResourceTags($em, $entity);
            $this->gatherRelationTags($em, $entity);
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $originalEntity = $this->getOriginalEntity($entity, $em);
            $this->addTagForItem($entity);
            $this->gatherResourceTags($em, $entity, $originalEntity);
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $originalEntity = $this->getOriginalEntity($entity, $em);
            $this->addTagForItem($originalEntity);
            $this->gatherResourceTags($em, $originalEntity);
            $this->gatherRelationTags($em, $originalEntity);
        }

        // trigger cache purges for changes on many-to-many relations
        // for some reason, changes to Many-To-Many relations are not included in the preUpdate changeSet
        foreach ($uow->getScheduledCollectionUpdates() as $collection) {
            $this->addTagsForManyToManyRelations($collection, $collection->getInsertDiff());
            $this->addTagsForManyToManyRelations($collection, $collection->getDeleteDiff());
        }
        foreach ($uow->getScheduledCollectionDeletions() as $collection) {
            $this->addTagsForManyToManyRelations($collection, $collection->getDeleteDiff());
        }
    }

    /**
     * Purges tags collected during this request, and clears the tag list.
     */
    public function postFlush(): void {
        $this->cacheManager->flush();
    }

    private function addTagsForManyToManyRelations($collection, $entities) {
        $associationMapping = $collection->getMapping();

        if (ClassMetadataInfo::MANY_TO_MANY !== $associationMapping['type']) {
            return;
        }

        foreach ($entities as $entity) {
            $relatedProperty = $associationMapping['isOwningSide'] ? $associationMapping['inversedBy'] : $associationMapping['mappedBy'];
            if (!$relatedProperty) {
                continue;
            }

            $this->addTagForItem($entity, $relatedProperty);
        }
    }

    /**
     * Computes the original state of the entity based on the current entity and on the changeset.
     */
    private function getOriginalEntity($entity, $em) {
        $uow = $em->getUnitOfWork();
        $changeSet = $uow->getEntityChangeSet($entity);
        $classMetadata = $em->getClassMetadata(ClassUtils::getClass($entity));

        $originalEntity = clone $entity;
        $em->detach($originalEntity);
        foreach ($changeSet as $key => $value) {
            $classMetadata->setFieldValue($originalEntity, $key, $value[0]);
        }

        return $originalEntity;
    }

    /**
     * Purges all collections (GetCollection operations), in which entity is listed on top level.
     *
     * If oldEntity is provided, purge is only done if the IRI of the collection has changed
     * (e.g. for updating period on a ScheduleEntry and the IRI changes from /periods/1/schedule_entries to /periods/2/schedule_entries)
     */
    private function gatherResourceTags(EntityManagerInterface $em, object $entity, ?object $oldEntity = null): void {
        $entityClass = $this->getObjectClass($entity);
        if (!$this->resourceClassResolver->isResourceClass($entityClass)) {
            return;
        }

        $resourceClass = $this->resourceClassResolver->getResourceClass($entity);
        $this->gatherResourceTagsForClass($resourceClass, $entity, $oldEntity);

        // also purge parent classes (e.g. /content_nodes)
        $classMetadata = $em->getClassMetadata(ClassUtils::getClass($entity));
        foreach ($classMetadata->parentClasses as $parentClass) {
            $this->gatherResourceTagsForClass($parentClass, $entity, $oldEntity);
        }
    }

    private function gatherResourceTagsForClass(string $resourceClass, object $entity, ?object $oldEntity = null): void {
        $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create($resourceClass);
        $resourceIterator = $resourceMetadataCollection->getIterator();
        while ($resourceIterator->valid()) {
            /** @var ApiResource $metadata */
            $metadata = $resourceIterator->current();

            foreach ($metadata->getOperations() ?? [] as $operation) {
                if ($operation instanceof GetCollection) {
                    $this->invalidateCollection($operation, $entity, $oldEntity);
                }
            }
            $resourceIterator->next();
        }
    }

    /**
     * Purges a single collection (GetCollection operation).
     *
     * If oldEntity is provided, purge is only done if the IRI of the collection has changed
     * (e.g. for updating period on a ScheduleEntry and the IRI changes from /periods/1/schedule_entries to /periods/2/schedule_entries)
     */
    private function invalidateCollection(GetCollection $operation, object $entity, ?object $oldEntity = null): void {
        $iri = $this->iriConverter->getIriFromResource($entity, UrlGeneratorInterface::ABS_PATH, $operation);

        if (!$iri) {
            return;
        }

        if (!$oldEntity) {
            $this->cacheManager->invalidateTags([$iri]);

            return;
        }

        $oldIri = $this->iriConverter->getIriFromResource($oldEntity, UrlGeneratorInterface::ABS_PATH, $operation);
        if ($oldIri && $iri !== $oldIri) {
            $this->cacheManager->invalidateTags([$iri]);
            $this->cacheManager->invalidateTags([$oldIri]);
        }
    }

    /**
     * Invalidate all relation tags of foreign objects ($relatedObject), in which $entity appears.
     *
     * @psalm-suppress UndefinedClass
     */
    private function gatherRelationTags(EntityManagerInterface $em, object $entity): void {
        $associationMappings = $em->getClassMetadata(ClassUtils::getClass($entity))->getAssociationMappings();

        foreach ($associationMappings as $property => $associationMapping) {
            if ($associationMapping instanceof AssociationMapping && ($associationMapping->targetEntity ?? null) && !$this->resourceClassResolver->isResourceClass($associationMapping->targetEntity)) {
                return;
            }

            $relatedProperty = $associationMapping['isOwningSide'] ? $associationMapping['inversedBy'] : $associationMapping['mappedBy'];
            if (!$relatedProperty) {
                continue;
            }

            if (!$this->propertyAccessor->isReadable($entity, $property)) {
                continue;
            }
            $relatedObject = $this->propertyAccessor->getValue($entity, $property);
            if ($relatedObject === $entity) {
                continue;
            }

            $this->addTagsFor(
                $relatedObject,
                $relatedProperty
            );
        }
    }

    private function addTagsFor(mixed $value, ?string $property = null): void {
        if (!$value || \is_scalar($value)) {
            return;
        }

        if (!is_iterable($value)) {
            $this->addTagForItem($value, $property);

            return;
        }

        if ($value instanceof PersistentCollection) {
            $value = clone $value;
        }

        foreach ($value as $v) {
            $this->addTagForItem($v, $property);
        }
    }

    private function addTagForItem(mixed $value, ?string $property = null): void {
        if (!$this->resourceClassResolver->isResourceClass($this->getObjectClass($value))) {
            return;
        }

        try {
            if ($value instanceof BaseEntity) {
                $iri = $value->getId();
            } else {
                $iri = $this->iriConverter->getIriFromResource($value);
            }
            if ($iri && $property) {
                $iri .= self::IRI_RELATION_DELIMITER.$property;
            }
            if ($iri) {
                $this->cacheManager->invalidateTags([$iri]);
            }
        } catch (InvalidArgumentException|RuntimeException) {
        }
    }
}
