<?php

// This file is based on ApiPlatform\Doctrine\EventListener\PurgeHttpCacheListener

declare(strict_types=1);

namespace App\EventListener;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Api\ResourceClassResolverInterface;
use ApiPlatform\Api\UrlGeneratorInterface;
use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Exception\OperationNotFoundException;
use ApiPlatform\Exception\RuntimeException;
use ApiPlatform\HttpCache\PurgerInterface;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Util\ClassInfoTrait;
use App\Entity\Category;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Purges responses containing modified entities from the proxy cache.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class PurgeHttpCacheListener {
    use ClassInfoTrait;
    private readonly PropertyAccessorInterface $propertyAccessor;
    private array $tags = [];

    public function __construct(private readonly PurgerInterface $purger, private readonly IriConverterInterface $iriConverter, private readonly ResourceClassResolverInterface $resourceClassResolver, PropertyAccessorInterface $propertyAccessor, private ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory) {
        $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
    }

    /**
     * Collects tags from the previous and the current version of the updated entities to purge related documents.
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs): void {
        $object = $eventArgs->getObject();
        $this->gatherResourceAndItemTags($object, true);

        $changeSet = $eventArgs->getEntityChangeSet();
        $objectManager = method_exists($eventArgs, 'getObjectManager') ? $eventArgs->getObjectManager() : $eventArgs->getEntityManager();
        $associationMappings = $objectManager->getClassMetadata(ClassUtils::getClass($eventArgs->getObject()))->getAssociationMappings();

        foreach ($changeSet as $key => $value) {
            if (!isset($associationMappings[$key])) {
                continue;
            }

            $this->addTagsFor($value[0]);
            $this->addTagsFor($value[1]);
        }
    }

    /**
     * Collects tags from inserted and deleted entities, including relations.
     */
    public function onFlush(OnFlushEventArgs $eventArgs): void {
        $em = method_exists($eventArgs, 'getObjectManager') ? $eventArgs->getObjectManager() : $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $this->gatherResourceAndItemTags($entity, false);
            // $this->gatherRelationTags($em, $entity);
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $this->gatherResourceAndItemTags($entity, true);
            // $this->gatherRelationTags($em, $entity);
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $this->gatherResourceAndItemTags($entity, true);
            // $this->gatherRelationTags($em, $entity);
        }
    }

    /**
     * Purges tags collected during this request, and clears the tag list.
     */
    public function postFlush(): void {
        if (empty($this->tags)) {
            return;
        }

        $this->purger->purge(array_values($this->tags));

        $this->tags = [];
    }

    private function gatherResourceAndItemTags(object $entity, bool $purgeItem): void {
        try {
            $resourceClass = $this->resourceClassResolver->getResourceClass($entity);
            $iri = $this->iriConverter->getIriFromResource($resourceClass, UrlGeneratorInterface::ABS_PATH, new GetCollection());
            $this->tags[$iri] = $iri;

            if ($purgeItem) {
                $this->addTagForItem($entity);
            }

            // purge camp specific collection
            // this is only an POC/example for category; later this could filter for instances of BelongsToCampInterface
            if (Category::class === $resourceClass) {
                $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create($resourceClass);
                $operation = $resourceMetadataCollection->getOperation('_api_/camps/{campId}/categories.{_format}_get_collection');
                $iri = $this->iriConverter->getIriFromResource($resourceClass, UrlGeneratorInterface::ABS_PATH, $operation, ['uri_variables' => ['campId' => $entity->getCamp()->getId()]]);
                $this->tags[$iri] = $iri;
            }
        } catch (OperationNotFoundException|InvalidArgumentException) {
        }
    }

    private function gatherRelationTags(EntityManagerInterface $em, object $entity): void {
        $associationMappings = $em->getClassMetadata(ClassUtils::getClass($entity))->getAssociationMappings();
        foreach (array_keys($associationMappings) as $property) {
            if ($this->propertyAccessor->isReadable($entity, $property)) {
                $this->addTagsFor($this->propertyAccessor->getValue($entity, $property));
            }
        }
    }

    private function addTagsFor(mixed $value): void {
        if (!$value || \is_scalar($value)) {
            return;
        }

        if (!is_iterable($value)) {
            $this->addTagForItem($value);

            return;
        }

        if ($value instanceof PersistentCollection) {
            $value = clone $value;
        }

        foreach ($value as $v) {
            $this->addTagForItem($v);
        }
    }

    private function addTagForItem(mixed $value): void {
        if (!$this->resourceClassResolver->isResourceClass($this->getObjectClass($value))) {
            return;
        }

        try {
            $iri = $this->iriConverter->getIriFromResource($value);
            $this->tags[$iri] = $iri;
        } catch (RuntimeException|InvalidArgumentException) {
        }
    }
}
