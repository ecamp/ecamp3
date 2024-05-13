<?php

/*
 * This file was partially copied from api-platform/core.
 *
 * For original author and license information see upstream file.
 *
 * Upstream file (main branch):               https://github.com/api-platform/core/blob/main/tests/Doctrine/EventListener/PurgeHttpCacheListenerTest.php
 * Upstream file (last synchronized version): https://github.com/api-platform/core/blob/1821a05eebd107fd495376b43bfc9f64d72d6e7c/tests/Doctrine/EventListener/PurgeHttpCacheListenerTest.php
 * Last synchronized commit:                  2023-10-27 / 1821a05eebd107fd495376b43bfc9f64d72d6e7c
 */

declare(strict_types=1);

namespace App\Tests\HttpCache;

use ApiPlatform\HttpCache\PurgerInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use ApiPlatform\Metadata\ResourceClassResolverInterface;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use App\HttpCache\PurgeHttpCacheListener;
use App\Tests\HttpCache\Entity\ContainNonResource;
use App\Tests\HttpCache\Entity\Dummy;
use App\Tests\HttpCache\Entity\DummyNoGetOperation;
use App\Tests\HttpCache\Entity\NotAResource;
use App\Tests\HttpCache\Entity\RelatedDummy;
use App\Tests\HttpCache\Entity\RelatedOwningDummy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use FOS\HttpCacheBundle\CacheManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 *
 * @internal
 */
class PurgeHttpCacheListenerTest extends TestCase {
    use ProphecyTrait;

    private ObjectProphecy $cacheManagerProphecy;
    private ObjectProphecy $resourceClassResolverProphecy;
    private ObjectProphecy $uowProphecy;
    private ObjectProphecy $emProphecy;
    private ObjectProphecy $propertyAccessorProphecy;
    private ObjectProphecy $iriConverterProphecy;
    private ObjectProphecy $metadataFactoryProphecy;

    protected function setUp(): void {
        $this->cacheManagerProphecy = $this->prophesize(CacheManager::class);
        $this->cacheManagerProphecy->flush(Argument::any())->willReturn(0);

        $this->resourceClassResolverProphecy = $this->prophesize(ResourceClassResolverInterface::class);
        $this->resourceClassResolverProphecy->isResourceClass(Argument::type('string'))->willReturn(true);
        $this->resourceClassResolverProphecy->getResourceClass(Argument::type(Dummy::class))->willReturn(Dummy::class);

        $this->uowProphecy = $this->prophesize(UnitOfWork::class);

        $this->emProphecy = $this->prophesize(EntityManagerInterface::class);
        $this->emProphecy->getUnitOfWork()->willReturn($this->uowProphecy->reveal());
        $dummyClassMetadata = new ClassMetadata(Dummy::class);
        $dummyClassMetadata->mapManyToOne(['fieldName' => 'relatedDummy', 'targetEntity' => RelatedDummy::class, 'inversedBy' => 'dummies']);
        $dummyClassMetadata->mapOneToOne(['fieldName' => 'relatedOwningDummy', 'targetEntity' => RelatedOwningDummy::class, 'inversedBy' => 'ownedDummy']);

        $this->emProphecy->getClassMetadata(Dummy::class)->willReturn($dummyClassMetadata);

        $this->propertyAccessorProphecy = $this->prophesize(PropertyAccessorInterface::class);
        $this->propertyAccessorProphecy->isReadable(Argument::type(Dummy::class), 'relatedDummy')->willReturn(true);
        $this->propertyAccessorProphecy->isReadable(Argument::type(Dummy::class), 'relatedOwningDummy')->willReturn(false);
        $this->propertyAccessorProphecy->getValue(Argument::type(Dummy::class), 'relatedDummy')->willReturn(null);
        $this->propertyAccessorProphecy->getValue(Argument::type(Dummy::class), 'relatedOwningDummy')->willReturn(null);

        $this->metadataFactoryProphecy = $this->prophesize(ResourceMetadataCollectionFactoryInterface::class);
        $operation = (new GetCollection())->withShortName('Dummy')->withClass(Dummy::class);
        $operation2 = (new GetCollection())->withShortName('DummyAsSubresource')->withClass(Dummy::class);
        $this->metadataFactoryProphecy->create(Dummy::class)->willReturn(new ResourceMetadataCollection('Dummy', [
            (new ApiResource('Dummy'))
                ->withShortName('Dummy')
                ->withOperations(new Operations([
                    'get_collection' => $operation,
                    'related_dummies/{id}/dummmies_get_collection' => $operation2,
                ])),
        ]));

        $this->iriConverterProphecy = $this->prophesize(IriConverterInterface::class);
        $this->iriConverterProphecy->getIriFromResource(Argument::type(Dummy::class), UrlGeneratorInterface::ABS_PATH, $operation)->willReturn('/dummies');
        $this->iriConverterProphecy->getIriFromResource(Argument::type(Dummy::class), UrlGeneratorInterface::ABS_PATH, $operation2)->will(function ($args) { return '/related_dummies/'.$args[0]->getRelatedDummy()->getId().'/dummies'; });
        $this->iriConverterProphecy->getIriFromResource(Argument::type(Dummy::class))->will(function ($args) { return '/dummies/'.$args[0]->getId(); });
    }

    /**
     * the following tests are copied from upstream PurgeHttpCacheListenerTest
     * only adjusted to passt the tests with adjusted logic from PurgeHttpCacheListener.
     * Other than that, kept changes to a minimum, in order to simplify copying changes to upstream test.
     */
    public function testOnFlush(): void {
        $toInsert1 = new Dummy();
        $toInsert2 = new Dummy();

        $toDelete1 = new Dummy();
        $toDelete1->setId('3');
        $toDelete2 = new Dummy();
        $toDelete2->setId('4');

        $toDeleteNoPurge = new DummyNoGetOperation();
        $toDeleteNoPurge->setId('5');

        $cacheManagerProphecy = $this->prophesize(CacheManager::class);
        $cacheManagerProphecy->invalidateTags(['/dummies'])->willReturn($cacheManagerProphecy)->shouldBeCalled();
        $cacheManagerProphecy->invalidateTags(['/dummies/3'])->willReturn($cacheManagerProphecy)->shouldBeCalled();
        $cacheManagerProphecy->invalidateTags(['/dummies/4'])->willReturn($cacheManagerProphecy)->shouldBeCalled();
        $cacheManagerProphecy->flush(Argument::any())->willReturn(0);

        $metadataFactoryProphecy = $this->prophesize(ResourceMetadataCollectionFactoryInterface::class);
        $operation = (new GetCollection())->withShortName('Dummy')->withClass(Dummy::class);
        $metadataFactoryProphecy->create(Dummy::class)->willReturn(new ResourceMetadataCollection('Dummy', [
            (new ApiResource('Dummy'))
                ->withShortName('Dummy')
                ->withOperations(new Operations([
                    'get' => $operation,
                ])),
        ]))->shouldBeCalled();
        $metadataFactoryProphecy->create(DummyNoGetOperation::class)->willReturn(new ResourceMetadataCollection('DummyNoGetOperation', [
            (new ApiResource('DummyNoGetOperation'))
                ->withShortName('DummyNoGetOperation'),
        ]))->shouldBeCalled();

        $iriConverterProphecy = $this->prophesize(IriConverterInterface::class);
        $iriConverterProphecy->getIriFromResource(Argument::type(Dummy::class), UrlGeneratorInterface::ABS_PATH, Argument::type(GetCollection::class))->willReturn('/dummies')->shouldBeCalled();
        $iriConverterProphecy->getIriFromResource($toDelete1)->willReturn('/dummies/3')->shouldBeCalled();
        $iriConverterProphecy->getIriFromResource($toDelete2)->willReturn('/dummies/4')->shouldBeCalled();
        $iriConverterProphecy->getIriFromResource($toDeleteNoPurge)->willReturn(null)->shouldBeCalled();

        $resourceClassResolverProphecy = $this->prophesize(ResourceClassResolverInterface::class);
        $resourceClassResolverProphecy->isResourceClass(Argument::type('string'))->willReturn(true)->shouldBeCalled();
        $resourceClassResolverProphecy->getResourceClass(Argument::type(Dummy::class))->willReturn(Dummy::class)->shouldBeCalled();
        $resourceClassResolverProphecy->getResourceClass(Argument::type(DummyNoGetOperation::class))->willReturn(DummyNoGetOperation::class)->shouldBeCalled();

        $uowProphecy = $this->prophesize(UnitOfWork::class);
        $uowProphecy->getScheduledEntityInsertions()->willReturn([$toInsert1, $toInsert2])->shouldBeCalled();
        $uowProphecy->getScheduledEntityDeletions()->willReturn([$toDelete1, $toDelete2, $toDeleteNoPurge])->shouldBeCalled();

        $emProphecy = $this->prophesize(EntityManagerInterface::class);
        $emProphecy->getUnitOfWork()->willReturn($uowProphecy->reveal())->shouldBeCalled();
        $dummyClassMetadata = new ClassMetadata(Dummy::class);
        $dummyClassMetadata->mapManyToOne(['fieldName' => 'relatedDummy', 'targetEntity' => RelatedDummy::class, 'inversedBy' => 'dummies']);
        $dummyClassMetadata->mapOneToOne(['fieldName' => 'relatedOwningDummy', 'targetEntity' => RelatedOwningDummy::class, 'inversedBy' => 'ownedDummy']);

        $emProphecy->getClassMetadata(Dummy::class)->willReturn($dummyClassMetadata)->shouldBeCalled();
        $emProphecy->getClassMetadata(DummyNoGetOperation::class)->willReturn(new ClassMetadata(DummyNoGetOperation::class))->shouldBeCalled();
        $eventArgs = new OnFlushEventArgs($emProphecy->reveal());

        $propertyAccessorProphecy = $this->prophesize(PropertyAccessorInterface::class);
        $propertyAccessorProphecy->isReadable(Argument::type(Dummy::class), 'relatedDummy')->willReturn(true);
        $propertyAccessorProphecy->isReadable(Argument::type(Dummy::class), 'relatedOwningDummy')->willReturn(false);
        $propertyAccessorProphecy->getValue(Argument::type(Dummy::class), 'relatedDummy')->willReturn(null);
        $propertyAccessorProphecy->getValue(Argument::type(Dummy::class), 'relatedOwningDummy')->willReturn(null);

        $listener = new PurgeHttpCacheListener($iriConverterProphecy->reveal(), $resourceClassResolverProphecy->reveal(), $propertyAccessorProphecy->reveal(), $metadataFactoryProphecy->reveal(), $cacheManagerProphecy->reveal());
        $listener->onFlush($eventArgs);
        $listener->postFlush();
    }

    public function testPreUpdate(): void {
        $oldRelatedDummy = new RelatedDummy();
        $oldRelatedDummy->setId('1');

        $newRelatedDummy = new RelatedDummy();
        $newRelatedDummy->setId('2');

        $dummy = new Dummy();
        $dummy->setId('1');

        $cacheManagerProphecy = $this->prophesize(CacheManager::class);
        $cacheManagerProphecy->invalidateTags(['/dummies/1'])->shouldBeCalled()->willReturn($cacheManagerProphecy);
        $cacheManagerProphecy->invalidateTags(['/related_dummies/old#dummies'])->shouldBeCalled()->willReturn($cacheManagerProphecy);
        $cacheManagerProphecy->invalidateTags(['/related_dummies/new#dummies'])->shouldBeCalled()->willReturn($cacheManagerProphecy);
        $cacheManagerProphecy->flush(Argument::any())->willReturn(0);

        $metadataFactoryProphecy = $this->prophesize(ResourceMetadataCollectionFactoryInterface::class);

        $iriConverterProphecy = $this->prophesize(IriConverterInterface::class);
        $iriConverterProphecy->getIriFromResource($dummy)->willReturn('/dummies/1')->shouldBeCalled();
        $iriConverterProphecy->getIriFromResource($oldRelatedDummy)->willReturn('/related_dummies/old')->shouldBeCalled();
        $iriConverterProphecy->getIriFromResource($newRelatedDummy)->willReturn('/related_dummies/new')->shouldBeCalled();

        $resourceClassResolverProphecy = $this->prophesize(ResourceClassResolverInterface::class);
        $resourceClassResolverProphecy->isResourceClass(Argument::type('string'))->willReturn(true)->shouldBeCalled();

        $emProphecy = $this->prophesize(EntityManagerInterface::class);

        $classMetadata = new ClassMetadata(Dummy::class);
        $classMetadata->mapManyToOne(['fieldName' => 'relatedDummy', 'targetEntity' => RelatedDummy::class, 'inversedBy' => 'dummies']);
        $emProphecy->getClassMetadata(Dummy::class)->willReturn($classMetadata)->shouldBeCalled();

        $changeSet = ['relatedDummy' => [$oldRelatedDummy, $newRelatedDummy]];
        $eventArgs = new PreUpdateEventArgs($dummy, $emProphecy->reveal(), $changeSet);

        $propertyAccessorProphecy = $this->prophesize(PropertyAccessorInterface::class);

        $listener = new PurgeHttpCacheListener($iriConverterProphecy->reveal(), $resourceClassResolverProphecy->reveal(), $propertyAccessorProphecy->reveal(), $metadataFactoryProphecy->reveal(), $cacheManagerProphecy->reveal());
        $listener->preUpdate($eventArgs);
        $listener->postFlush();
    }

    public function testNothingToPurge(): void {
        $dummyNoGetOperation = new DummyNoGetOperation();
        $dummyNoGetOperation->setId('1');

        $purgerProphecy = $this->prophesize(PurgerInterface::class);
        $purgerProphecy->purge([])->shouldNotBeCalled();

        $cacheManagerProphecy = $this->prophesize(CacheManager::class);
        $cacheManagerProphecy->invalidateTags(Argument::any())->shouldNotBeCalled();
        $cacheManagerProphecy->flush(Argument::any())->willReturn(0);

        $metadataFactoryProphecy = $this->prophesize(ResourceMetadataCollectionFactoryInterface::class);

        $iriConverterProphecy = $this->prophesize(IriConverterInterface::class);
        $iriConverterProphecy->getIriFromResource($dummyNoGetOperation)->willReturn(null)->shouldBeCalled();

        $resourceClassResolverProphecy = $this->prophesize(ResourceClassResolverInterface::class);
        $resourceClassResolverProphecy->isResourceClass(Argument::type('string'))->willReturn(true)->shouldBeCalled();

        $emProphecy = $this->prophesize(EntityManagerInterface::class);

        $classMetadata = new ClassMetadata(DummyNoGetOperation::class);
        $emProphecy->getClassMetadata(DummyNoGetOperation::class)->willReturn($classMetadata)->shouldBeCalled();

        $changeSet = ['lorem' => 'ipsum'];
        $eventArgs = new PreUpdateEventArgs($dummyNoGetOperation, $emProphecy->reveal(), $changeSet);

        $propertyAccessorProphecy = $this->prophesize(PropertyAccessorInterface::class);

        $listener = new PurgeHttpCacheListener($iriConverterProphecy->reveal(), $resourceClassResolverProphecy->reveal(), $propertyAccessorProphecy->reveal(), $metadataFactoryProphecy->reveal(), $cacheManagerProphecy->reveal());
        $listener->preUpdate($eventArgs);
        $listener->postFlush();
    }

    public function testNotAResourceClass(): void {
        $containNonResource = new ContainNonResource();
        $nonResource = new NotAResource('foo', 'bar');

        $cacheManagerProphecy = $this->prophesize(CacheManager::class);
        $cacheManagerProphecy->invalidateTags(Argument::any())->shouldNotBeCalled();
        $cacheManagerProphecy->flush(Argument::any())->willReturn(0);

        $metadataFactoryProphecy = $this->prophesize(ResourceMetadataCollectionFactoryInterface::class);
        $metadataFactoryProphecy->create(ContainNonResource::class)->willReturn(new ResourceMetadataCollection('ContainNonResource', [
            (new ApiResource('ContainNonResource'))
                ->withShortName('ContainNonResource'),
        ]))->shouldBeCalled();

        $iriConverterProphecy = $this->prophesize(IriConverterInterface::class);
        $iriConverterProphecy->getIriFromResource(ContainNonResource::class, UrlGeneratorInterface::ABS_PATH, Argument::any())->willReturn('/dummies/1');
        $iriConverterProphecy->getIriFromResource($nonResource)->shouldNotBeCalled();

        $resourceClassResolverProphecy = $this->prophesize(ResourceClassResolverInterface::class);
        $resourceClassResolverProphecy->getResourceClass(Argument::type(ContainNonResource::class))->willReturn(ContainNonResource::class)->shouldBeCalled();
        $resourceClassResolverProphecy->isResourceClass(NotAResource::class)->willReturn(false)->shouldBeCalled();

        $uowProphecy = $this->prophesize(UnitOfWork::class);
        $uowProphecy->getScheduledEntityInsertions()->willReturn([$containNonResource])->shouldBeCalled();
        $uowProphecy->getScheduledEntityDeletions()->willReturn([])->shouldBeCalled();

        $emProphecy = $this->prophesize(EntityManagerInterface::class);
        $emProphecy->getUnitOfWork()->willReturn($uowProphecy->reveal())->shouldBeCalled();

        $dummyClassMetadata = new ClassMetadata(ContainNonResource::class);
        $dummyClassMetadata->mapManyToOne(['fieldName' => 'notAResource', 'targetEntity' => NotAResource::class, 'inversedBy' => 'resources']);
        $dummyClassMetadata->mapOneToMany(['fieldName' => 'collectionOfNotAResource', 'targetEntity' => NotAResource::class, 'mappedBy' => 'resource']);
        $emProphecy->getClassMetadata(ContainNonResource::class)->willReturn($dummyClassMetadata);
        $eventArgs = new OnFlushEventArgs($emProphecy->reveal());

        $propertyAccessorProphecy = $this->prophesize(PropertyAccessorInterface::class);
        $propertyAccessorProphecy->isReadable(Argument::type(ContainNonResource::class), 'notAResource')->willReturn(true);
        $propertyAccessorProphecy->isReadable(Argument::type(ContainNonResource::class), 'collectionOfNotAResource')->willReturn(true);
        $propertyAccessorProphecy->getValue(Argument::type(ContainNonResource::class), 'notAResource')->shouldNotBeCalled();
        $propertyAccessorProphecy->getValue(Argument::type(ContainNonResource::class), 'collectionOfNotAResource')->shouldNotBeCalled();

        $listener = new PurgeHttpCacheListener($iriConverterProphecy->reveal(), $resourceClassResolverProphecy->reveal(), $propertyAccessorProphecy->reveal(), $metadataFactoryProphecy->reveal(), $cacheManagerProphecy->reveal());
        $listener->onFlush($eventArgs);
    }

    /**
     * the following tests are additional tests, created to test specific new behavior of PurgeHttpCacheListener.
     */
    public function testInsertingShouldPurgeSubresourceCollections(): void {
        // given
        $toInsert1 = new Dummy();
        $toInsert1->setId('1');
        $relatedDummy = new RelatedDummy();
        $relatedDummy->setId('100');
        $toInsert1->setRelatedDummy($relatedDummy);

        $this->uowProphecy->getScheduledEntityInsertions()->willReturn([$toInsert1]);
        $this->uowProphecy->getScheduledEntityDeletions()->willReturn([]);

        // then
        $this->cacheManagerProphecy->invalidateTags(['/dummies'])->willReturn($this->cacheManagerProphecy)->shouldBeCalled();
        $this->cacheManagerProphecy->invalidateTags(['/related_dummies/100/dummies'])->willReturn($this->cacheManagerProphecy)->shouldBeCalled();

        // when
        $listener = new PurgeHttpCacheListener($this->iriConverterProphecy->reveal(), $this->resourceClassResolverProphecy->reveal(), $this->propertyAccessorProphecy->reveal(), $this->metadataFactoryProphecy->reveal(), $this->cacheManagerProphecy->reveal());
        $listener->onFlush(new OnFlushEventArgs($this->emProphecy->reveal()));
        $listener->postFlush();
    }

    public function testDeleteShouldPurgeSubresourceCollections(): void {
        // given
        $toDelete1 = new Dummy();
        $toDelete1->setId('1');
        $relatedDummy = new RelatedDummy();
        $relatedDummy->setId('100');
        $toDelete1->setRelatedDummy($relatedDummy);

        $this->uowProphecy->getScheduledEntityInsertions()->willReturn([]);
        $this->uowProphecy->getScheduledEntityDeletions()->willReturn([$toDelete1]);

        // then
        $this->cacheManagerProphecy->invalidateTags(['/dummies/1'])->willReturn($this->cacheManagerProphecy)->shouldBeCalled();
        $this->cacheManagerProphecy->invalidateTags(['/dummies'])->willReturn($this->cacheManagerProphecy)->shouldBeCalled();
        $this->cacheManagerProphecy->invalidateTags(['/related_dummies/100/dummies'])->willReturn($this->cacheManagerProphecy)->shouldBeCalled();

        // when
        $listener = new PurgeHttpCacheListener($this->iriConverterProphecy->reveal(), $this->resourceClassResolverProphecy->reveal(), $this->propertyAccessorProphecy->reveal(), $this->metadataFactoryProphecy->reveal(), $this->cacheManagerProphecy->reveal());
        $listener->onFlush(new OnFlushEventArgs($this->emProphecy->reveal()));
        $listener->postFlush();
    }
}
