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
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\MakerBundle\Doctrine\StaticReflectionService;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\containsEqual;
use function PHPUnit\Framework\logicalAnd;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 *
 * @internal
 */
class PurgeHttpCacheListenerTest extends TestCase {
    private CacheManager $cacheManagerProphecy;
    private ResourceClassResolverInterface $resourceClassResolverProphecy;
    private UnitOfWork $uowProphecy;
    private EntityManagerInterface $emProphecy;
    private PropertyAccessorInterface $propertyAccessorProphecy;
    private IriConverterInterface $iriConverterProphecy;
    private ResourceMetadataCollectionFactoryInterface $metadataFactoryProphecy;

    protected function setUp(): void {
        $this->cacheManagerProphecy = $this->createMock(CacheManager::class);
        $this->cacheManagerProphecy->method('flush')->willReturn(0);

        $this->resourceClassResolverProphecy = $this->createStub(ResourceClassResolverInterface::class);
        $this->resourceClassResolverProphecy->method('isResourceClass')->willReturn(true);
        $this->resourceClassResolverProphecy->method('getResourceClass')->willReturn(Dummy::class);

        $this->uowProphecy = $this->createMock(UnitOfWork::class);

        $this->emProphecy = $this->createStub(EntityManagerInterface::class);
        $this->emProphecy->method('getUnitOfWork')->willReturnCallback(fn () => $this->uowProphecy);

        $dummyClassMetadata = new ClassMetadata(Dummy::class);
        $dummyClassMetadata->mapManyToOne(['fieldName' => 'relatedDummy', 'targetEntity' => RelatedDummy::class, 'inversedBy' => 'dummies']);
        $dummyClassMetadata->mapOneToOne(['fieldName' => 'relatedOwningDummy', 'targetEntity' => RelatedOwningDummy::class, 'inversedBy' => 'ownedDummy']);
        $dummyClassMetadata->wakeupReflection(new StaticReflectionService());
        $this->emProphecy
            ->method('getClassMetadata')
            ->willReturnCallback(fn ($class) => match ($class) {
                Dummy::class => $dummyClassMetadata,
            })
        ;

        $this->propertyAccessorProphecy = $this->createStub(PropertyAccessorInterface::class);
        $this->propertyAccessorProphecy
            ->method('isReadable')
            ->willReturnCallback(
                function (array|object $obj, PropertyPathInterface|string $prop): bool {
                    if ($obj instanceof Dummy && 'relatedDummy' === $prop) {
                        return true;
                    }

                    return false;
                }
            )
        ;
        $this->propertyAccessorProphecy->method('getValue')->willReturn(null);

        $this->metadataFactoryProphecy = $this->createStub(ResourceMetadataCollectionFactoryInterface::class);
        $operation = new GetCollection()->withShortName('Dummy')->withClass(Dummy::class);
        $operation2 = new GetCollection()->withShortName('DummyAsSubresource')->withClass(Dummy::class);
        $this->metadataFactoryProphecy->method('create')
            ->willReturnCallback(fn ($class) => match ($class) {
                Dummy::class => new ResourceMetadataCollection('Dummy', [
                    new ApiResource('Dummy')
                        ->withShortName('Dummy')
                        ->withOperations(new Operations([
                            'get_collection' => $operation,
                            'related_dummies/{id}/dummmies_get_collection' => $operation2,
                        ])),
                ])
            })
        ;

        $this->iriConverterProphecy = $this->createStub(IriConverterInterface::class);
        $this->iriConverterProphecy->method('getIriFromResource')->willReturnCallback(function (object|string $resource, ...$args) use ($operation, $operation2): ?string {
            if ($resource instanceof Dummy) {
                if (isset($args[1]) && $args[1] === $operation) {
                    return '/dummies';
                }
                if (isset($args[1]) && $args[1] === $operation2) {
                    return '/related_dummies/'.$resource->getRelatedDummy()->getId().'/dummies';
                }

                return '/dummies/'.$resource->getId();
            }

            return null;
        });
    }

    /**
     * the following tests are copied from upstream PurgeHttpCacheListenerTest
     * only adjusted to passt the tests with adjusted logic from PurgeHttpCacheListener.
     * Other than that, kept changes to a minimum, in order to simplify copying changes to upstream test.
     */
    #[AllowMockObjectsWithoutExpectations]
    public function testOnFlush(): void {
        $toInsert1 = new Dummy();
        $toInsert2 = new Dummy();

        $toDelete1 = new Dummy();
        $toDelete1->setId('3');
        $toDelete2 = new Dummy();
        $toDelete2->setId('4');

        $toDeleteNoPurge = new DummyNoGetOperation();
        $toDeleteNoPurge->setId('5');

        $cacheManagerProphecy = $this->createMock(CacheManager::class);
        $cacheManagerInvalidateTagsCalls = [];
        $cacheManagerProphecy
            ->method('invalidateTags')
            ->willReturnCallback(function (array $tags) use (&$cacheManagerInvalidateTagsCalls, $cacheManagerProphecy) {
                $cacheManagerInvalidateTagsCalls[] = $tags;

                return $cacheManagerProphecy;
            })
        ;
        $cacheManagerProphecy->method('flush')->willReturn(0);

        $metadataFactoryProphecy = $this->createMock(ResourceMetadataCollectionFactoryInterface::class);
        $operation = new GetCollection()->withShortName('Dummy')->withClass(Dummy::class);
        $metadataFactoryProphecy
            ->method('create')
            ->willReturnCallback(function (string $class) use ($operation) {
                switch ($class) {
                    case Dummy::class:
                        return new ResourceMetadataCollection('Dummy', [
                            new ApiResource('Dummy')
                                ->withShortName('Dummy')
                                ->withOperations(new Operations([
                                    'get' => $operation,
                                ])),
                        ]);

                    case DummyNoGetOperation::class:
                        return new ResourceMetadataCollection('DummyNoGetOperation', [
                            new ApiResource('DummyNoGetOperation')
                                ->withShortName('DummyNoGetOperation'),
                        ]);

                    default:
                        TestCase::fail('Unexpected class passed to metadata factory: '.$class);
                }
            })
        ;

        $iriConverterProphecy = $this->createMock(IriConverterInterface::class);
        $iriConverterProphecy
            ->method('getIriFromResource')
            ->willReturnCallback(function (object|string $resource, ...$args) use (&$toDelete1, &$toDelete2, &$toDeleteNoPurge): ?string {
                if ($resource == $toDelete1) {
                    return '/dummies/3';
                }
                if ($resource == $toDelete2) {
                    return '/dummies/4';
                }
                if ($resource == $toDeleteNoPurge) {
                    return null;
                }
                if ($resource instanceof Dummy && isset($args[0], $args[1]) && UrlGeneratorInterface::ABS_PATH === $args[0] && $args[1] instanceof GetCollection) {
                    return '/dummies';
                }

                return null;
            })
        ;

        $resourceClassResolverProphecy = $this->createMock(ResourceClassResolverInterface::class);
        $resourceClassResolverProphecy->expects($this->atLeastOnce())->method('isResourceClass')->willReturn(true);
        $resourceClassResolverProphecy->method('getResourceClass')->willReturnCallback(
            function ($resource): string {
                return $resource::class;
            }
        );

        $uowMock = $this->createMock(UnitOfWork::class);
        $uowMock->method('getScheduledEntityInsertions')->willReturn([$toInsert1, $toInsert2]);
        $uowMock->method('getScheduledEntityUpdates')->willReturn([]);
        $uowMock->method('getScheduledEntityDeletions')->willReturn([$toDelete1, $toDelete2, $toDeleteNoPurge]);
        $uowMock->method('getScheduledCollectionUpdates')->willReturn([]);
        $uowMock->method('getScheduledCollectionDeletions')->willReturn([]);
        $uowMock->method('getEntityChangeSet')->willReturn([]);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->atLeastOnce())->method('getUnitOfWork')->willReturn($uowMock);
        $dummyClassMetadata = new ClassMetadata(Dummy::class);
        $dummyClassMetadata->mapManyToOne(['fieldName' => 'relatedDummy', 'targetEntity' => RelatedDummy::class, 'inversedBy' => 'dummies']);
        $dummyClassMetadata->mapOneToOne(['fieldName' => 'relatedOwningDummy', 'targetEntity' => RelatedOwningDummy::class, 'inversedBy' => 'ownedDummy']);

        $entityManager->expects($this->atLeast(2))->method('getClassMetadata')
            ->willReturnCallback(function (string $class) use ($dummyClassMetadata) {
                return match ($class) {
                    Dummy::class => $dummyClassMetadata,
                    RelatedDummy::class => new ClassMetadata(DummyNoGetOperation::class),
                    DummyNoGetOperation::class => new ClassMetadata(DummyNoGetOperation::class),
                    default => throw new \InvalidArgumentException('Unexpected class: '.$class),
                };
            })
        ;

        $propertyAccessorProphecy = $this->createMock(PropertyAccessorInterface::class);
        $propertyAccessorProphecy->method('isReadable')
            ->willReturnCallback(
                function (array|object $obj, PropertyPathInterface|string $prop): bool {
                    return $obj instanceof Dummy
                        && in_array($prop, ['relatedDummy', 'relatedOwningDummy'], true)
                        && ('relatedDummy' === $prop || 'relatedOwningDummy' === $prop);
                }
            )
        ;
        $propertyAccessorProphecy->method('getValue')->willReturn(null);

        $listener = new PurgeHttpCacheListener(
            iriConverter: $iriConverterProphecy,
            resourceClassResolver: $resourceClassResolverProphecy,
            propertyAccessor: $propertyAccessorProphecy,
            resourceMetadataCollectionFactory: $metadataFactoryProphecy,
            cacheManager: $cacheManagerProphecy,
            em: $entityManager,
        );
        $listener->onFlush();
        $listener->postFlush();

        assertThat($cacheManagerInvalidateTagsCalls, logicalAnd(containsEqual(['/dummies']), containsEqual(['/dummies/3']), containsEqual(['/dummies/4'])));
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testPreUpdate(): void {
        $oldRelatedDummy = new RelatedDummy();
        $oldRelatedDummy->setId('1');

        $newRelatedDummy = new RelatedDummy();
        $newRelatedDummy->setId('2');

        $dummy = new Dummy();
        $dummy->setId('1');

        $cacheManagerProphecy = $this->createMock(CacheManager::class);
        $cacheManagerProphecy->expects($this->exactly(2))
            ->method('invalidateTags')
            ->willReturnCallback(function (array $tags) use ($cacheManagerProphecy) {
                static $i = 0;
                $expected = [
                    ['/related_dummies/old#dummies'],
                    ['/related_dummies/new#dummies'],
                ];
                TestCase::assertEquals($expected[$i], $tags);
                ++$i;

                return $cacheManagerProphecy;
            })
        ;
        $cacheManagerProphecy->method('flush')->willReturn(0);

        $metadataFactoryProphecy = $this->createStub(ResourceMetadataCollectionFactoryInterface::class);

        $iriConverterProphecy = $this->createMock(IriConverterInterface::class);
        $iriConverterProphecy->expects($this->exactly(2))
            ->method('getIriFromResource')
            ->willReturnCallback(function (object|string $resource) use ($oldRelatedDummy, $newRelatedDummy): string {
                static $i = 0;
                $expected = [$oldRelatedDummy, $newRelatedDummy];
                TestCase::assertSame($expected[$i], $resource);
                $ret = ['/related_dummies/old', '/related_dummies/new'][$i];
                ++$i;

                return $ret;
            })
        ;

        $resourceClassResolverProphecy = $this->createMock(ResourceClassResolverInterface::class);
        $resourceClassResolverProphecy->expects($this->atLeastOnce())->method('isResourceClass')->willReturn(true);

        $emProphecy = $this->createStub(EntityManagerInterface::class);

        $classMetadata = new ClassMetadata(Dummy::class);
        $classMetadata->mapManyToOne(['fieldName' => 'relatedDummy', 'targetEntity' => RelatedDummy::class, 'inversedBy' => 'dummies']);
        $emProphecy->method('getClassMetadata')
            ->willReturnCallback(fn ($class) => match ($class) {
                Dummy::class => $classMetadata
            })
        ;

        $changeSet = ['relatedDummy' => [$oldRelatedDummy, $newRelatedDummy]];
        $em = $emProphecy;
        $eventArgs = new PreUpdateEventArgs($dummy, $em, $changeSet);

        $propertyAccessorProphecy = $this->createStub(PropertyAccessorInterface::class);

        $listener = new PurgeHttpCacheListener(
            iriConverter: $iriConverterProphecy,
            resourceClassResolver: $resourceClassResolverProphecy,
            propertyAccessor: $propertyAccessorProphecy,
            resourceMetadataCollectionFactory: $metadataFactoryProphecy,
            cacheManager: $cacheManagerProphecy,
            em: $emProphecy,
        );
        $listener->preUpdate($eventArgs);
        $listener->postFlush();
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNothingToPurge(): void {
        $dummyNoGetOperation = new DummyNoGetOperation();
        $dummyNoGetOperation->setId('1');

        $purgerProphecy = $this->createMock(PurgerInterface::class);
        $purgerProphecy->expects($this->never())->method('purge');

        $cacheManagerProphecy = $this->createMock(CacheManager::class);
        $cacheManagerProphecy->expects($this->never())->method('invalidateTags');
        $cacheManagerProphecy->method('flush')->willReturn(0);

        $metadataFactoryProphecy = $this->createStub(ResourceMetadataCollectionFactoryInterface::class);

        $iriConverterProphecy = $this->createStub(IriConverterInterface::class);

        $resourceClassResolverProphecy = $this->createStub(ResourceClassResolverInterface::class);

        $emProphecy = $this->createStub(EntityManagerInterface::class);

        $classMetadata = new ClassMetadata(DummyNoGetOperation::class);
        $emProphecy->method('getClassMetadata')
            ->willReturnCallback(fn ($class) => match ($class) {
                DummyNoGetOperation::class => $classMetadata
            })
        ;

        $changeSet = ['lorem' => 'ipsum'];
        $em = $emProphecy;
        $eventArgs = new PreUpdateEventArgs($dummyNoGetOperation, $em, $changeSet);

        $propertyAccessorProphecy = $this->createStub(PropertyAccessorInterface::class);

        $listener = new PurgeHttpCacheListener(
            iriConverter: $iriConverterProphecy,
            resourceClassResolver: $resourceClassResolverProphecy,
            propertyAccessor: $propertyAccessorProphecy,
            resourceMetadataCollectionFactory: $metadataFactoryProphecy,
            cacheManager: $cacheManagerProphecy,
            em: $emProphecy,
        );
        $listener->preUpdate($eventArgs);
        $listener->postFlush();
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testNotAResourceClass(): void {
        $nonResource = new NotAResource('foo', 'bar');

        $cacheManagerProphecy = $this->createMock(CacheManager::class);
        $cacheManagerProphecy->expects($this->never())->method('invalidateTags');
        $cacheManagerProphecy->method('flush')->willReturn(0);

        $iriConverterProphecy = $this->createMock(IriConverterInterface::class);
        $iriConverterProphecy->expects($this->never())->method('getIriFromResource');

        $metadataFactoryProphecy = $this->createStub(ResourceMetadataCollectionFactoryInterface::class);

        $resourceClassResolverProphecy = $this->createMock(ResourceClassResolverInterface::class);
        $resourceClassResolverProphecy->expects($this->once())->method('isResourceClass')->with(NotAResource::class)->willReturn(false);

        $uowProphecy = $this->createMock(UnitOfWork::class);
        $uowProphecy->expects($this->once())->method('getScheduledEntityInsertions')->willReturn([$nonResource]);
        $uowProphecy->expects($this->once())->method('getScheduledEntityDeletions')->willReturn([]);
        $uowProphecy->expects($this->once())->method('getScheduledEntityUpdates')->willReturn([]);
        $uowProphecy->expects($this->once())->method('getScheduledCollectionUpdates')->willReturn([]);
        $uowProphecy->expects($this->once())->method('getScheduledCollectionDeletions')->willReturn([]);

        $emProphecy = $this->createMock(EntityManagerInterface::class);
        $emProphecy->method('getUnitOfWork')->willReturn($uowProphecy);

        $dummyClassMetadata = new ClassMetadata(ContainNonResource::class);
        $emProphecy->expects($this->once())->method('getClassMetadata')->with(NotAResource::class)->willReturn($dummyClassMetadata);
        $em = $emProphecy;
        new OnFlushEventArgs($em);

        $propertyAccessorProphecy = $this->createStub(PropertyAccessorInterface::class);

        $listener = new PurgeHttpCacheListener(
            iriConverter: $iriConverterProphecy,
            resourceClassResolver: $resourceClassResolverProphecy,
            propertyAccessor: $propertyAccessorProphecy,
            resourceMetadataCollectionFactory: $metadataFactoryProphecy,
            cacheManager: $cacheManagerProphecy,
            em: $emProphecy,
        );
        $listener->onFlush();
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testPropertyIsNotAResourceClass(): void {
        $containNonResource = new ContainNonResource();
        $nonResource = new NotAResource('foo', 'bar');

        $cacheManagerProphecy = $this->createMock(CacheManager::class);
        $cacheManagerProphecy->expects($this->never())->method('invalidateTags');
        $cacheManagerProphecy->method('flush')->willReturn(0);

        $metadataFactoryProphecy = $this->createMock(ResourceMetadataCollectionFactoryInterface::class);
        $metadataFactoryProphecy->expects($this->once())->method('create')->with(ContainNonResource::class)->willReturn(new ResourceMetadataCollection('ContainNonResource', [
            new ApiResource('ContainNonResource')
                ->withShortName('ContainNonResource'),
        ]));

        $iriConverterProphecy = $this->createMock(IriConverterInterface::class);
        $that = $this;
        $iriConverterProphecy->method('getIriFromResource')->willReturnCallback(function (object|string $resource, ...$args) use ($nonResource, $that): ?string {
            $that->assertNotSame($nonResource, $resource, 'getIriFromResource should not be called with non-resource');
            if (ContainNonResource::class === $resource) {
                return '/dummies/1';
            }

            return null;
        });

        $resourceClassResolverProphecy = $this->createMock(ResourceClassResolverInterface::class);
        $resourceClassResolverProphecy->method('getResourceClass')->willReturn(ContainNonResource::class);
        $resourceClassResolverProphecy->expects($this->exactly(2))
            ->method('isResourceClass')
            ->willReturnCallback(
                function (string $class): bool {
                    if (ContainNonResource::class === $class) {
                        return true;
                    }
                    if (NotAResource::class === $class) {
                        return false;
                    }
                    TestCase::fail('Unexpected class passed to isResourceClass: '.$class);
                }
            )
        ;

        $uowProphecy = $this->createMock(UnitOfWork::class);
        $uowProphecy->expects($this->once())->method('getScheduledEntityInsertions')->willReturn([$containNonResource]);
        $uowProphecy->expects($this->once())->method('getScheduledEntityDeletions')->willReturn([]);
        $uowProphecy->expects($this->once())->method('getScheduledEntityUpdates')->willReturn([]);
        $uowProphecy->expects($this->once())->method('getScheduledCollectionUpdates')->willReturn([]);
        $uowProphecy->expects($this->once())->method('getScheduledCollectionDeletions')->willReturn([]);

        $emProphecy = $this->createMock(EntityManagerInterface::class);
        $emProphecy->expects($this->once())->method('getUnitOfWork')->willReturn($uowProphecy);

        $dummyClassMetadata = new ClassMetadata(ContainNonResource::class);
        $dummyClassMetadata->mapManyToOne(['fieldName' => 'notAResource', 'targetEntity' => NotAResource::class, 'inversedBy' => 'resources']);
        $dummyClassMetadata->mapOneToMany(['fieldName' => 'collectionOfNotAResource', 'targetEntity' => NotAResource::class, 'mappedBy' => 'resource']);
        $emProphecy->method('getClassMetadata')->willReturnCallback(function (string $class) use ($dummyClassMetadata) {
            return match ($class) {
                ContainNonResource::class => $dummyClassMetadata,
                default => new ClassMetadata($class),
            };
        });
        $em = $emProphecy;
        new OnFlushEventArgs($em);

        $propertyAccessorProphecy = $this->createMock(PropertyAccessorInterface::class);
        $propertyAccessorProphecy->method('isReadable')->willReturnCallback(function (array|object $obj, PropertyPathInterface|string $prop): bool {
            return $obj instanceof ContainNonResource && in_array($prop, ['notAResource', 'collectionOfNotAResource'], true);
        });
        $propertyAccessorProphecy->expects($this->never())->method('getValue');

        $listener = new PurgeHttpCacheListener(
            iriConverter: $iriConverterProphecy,
            resourceClassResolver: $resourceClassResolverProphecy,
            propertyAccessor: $propertyAccessorProphecy,
            resourceMetadataCollectionFactory: $metadataFactoryProphecy,
            cacheManager: $cacheManagerProphecy,
            em: $emProphecy,
        );
        $listener->onFlush();
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

        $this->uowProphecy->method('getScheduledEntityInsertions')->willReturn([$toInsert1]);
        $this->uowProphecy->method('getScheduledEntityDeletions')->willReturn([]);
        $this->uowProphecy->expects($this->once())->method('getScheduledEntityUpdates')->willReturn([]);
        $this->uowProphecy->method('getScheduledCollectionUpdates')->willReturn([]);
        $this->uowProphecy->method('getScheduledCollectionDeletions')->willReturn([]);

        // then
        $this->cacheManagerProphecy->expects($this->exactly(2))
            ->method('invalidateTags')
            ->willReturnCallback(function (array $tags) {
                static $i = 0;
                $expected = [
                    ['/dummies'],
                    ['/related_dummies/100/dummies'],
                ];
                TestCase::assertEquals($expected[$i], $tags);
                ++$i;

                return $this->cacheManagerProphecy;
            })
        ;

        // when

        $em = $this->emProphecy;
        $listener = new PurgeHttpCacheListener(
            iriConverter: $this->iriConverterProphecy,
            resourceClassResolver: $this->resourceClassResolverProphecy,
            propertyAccessor: $this->propertyAccessorProphecy,
            resourceMetadataCollectionFactory: $this->metadataFactoryProphecy,
            cacheManager: $this->cacheManagerProphecy,
            em: $em,
        );
        $listener->onFlush();
        $listener->postFlush();
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testDeleteShouldPurgeSubresourceCollections(): void {
        // given
        $toDelete1 = new Dummy();
        $toDelete1->setId('1');
        $relatedDummy = new RelatedDummy();
        $relatedDummy->setId('100');
        $toDelete1->setRelatedDummy($relatedDummy);

        $unitOfWork = $this->createStub(UnitOfWork::class);
        $unitOfWork->method('getScheduledEntityInsertions')->willReturn([]);
        $unitOfWork->method('getScheduledEntityUpdates')->willReturn([]);
        $unitOfWork->method('getScheduledEntityDeletions')->willReturn([$toDelete1]);
        $unitOfWork->method('getScheduledCollectionUpdates')->willReturn([]);
        $unitOfWork->method('getScheduledCollectionDeletions')->willReturn([]);
        $unitOfWork->method('getEntityChangeSet')->willReturn([]);

        $em = $this->createStub(EntityManagerInterface::class);
        $em->method('getUnitOfWork')->willReturn($unitOfWork);

        // then
        $this->cacheManagerProphecy->expects($this->exactly(3))
            ->method('invalidateTags')
            ->willReturnCallback(function (array $tags) {
                static $i = 0;
                $expected = [
                    ['/dummies/1'],
                    ['/dummies'],
                    ['/related_dummies/100/dummies'],
                ];
                TestCase::assertEquals($expected[$i], $tags);
                ++$i;

                return $this->cacheManagerProphecy;
            })
        ;

        // when
        $listener = new PurgeHttpCacheListener(
            iriConverter: $this->iriConverterProphecy,
            resourceClassResolver: $this->resourceClassResolverProphecy,
            propertyAccessor: $this->propertyAccessorProphecy,
            resourceMetadataCollectionFactory: $this->metadataFactoryProphecy,
            cacheManager: $this->cacheManagerProphecy,
            em: $em,
        );
        $listener->onFlush();
        $listener->postFlush();
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testUpdateShouldPurgeSubresourceCollections(): void {
        // given
        $toUpdate1 = new Dummy();
        $toUpdate1->setId('1');
        $relatedDummy = new RelatedDummy();
        $relatedDummy->setId('100');
        $toUpdate1->setRelatedDummy($relatedDummy);

        $relatedDummyOld = new RelatedDummy();
        $relatedDummyOld->setId('99');

        $this->uowProphecy = $this->createStub(UnitOfWork::class);
        $this->uowProphecy->method('getScheduledEntityInsertions')->willReturn([]);
        $this->uowProphecy->method('getScheduledEntityUpdates')->willReturn([$toUpdate1]);
        $this->uowProphecy->method('getScheduledEntityDeletions')->willReturn([]);
        $this->uowProphecy->method('getScheduledCollectionUpdates')->willReturn([]);
        $this->uowProphecy->method('getScheduledCollectionDeletions')->willReturn([]);
        $this->uowProphecy->method('getEntityChangeSet')->willReturn(['relatedDummy' => [$relatedDummyOld, $relatedDummy]]);

        // then
        $this->cacheManagerProphecy->expects($this->exactly(3))
            ->method('invalidateTags')
            ->willReturnCallback(function (array $tags) {
                static $i = 0;
                $expected = [
                    ['/dummies/1'],
                    ['/related_dummies/100/dummies'],
                    ['/related_dummies/99/dummies'],
                ];
                TestCase::assertEquals($expected[$i], $tags);
                ++$i;

                return $this->cacheManagerProphecy;
            })
        ;

        // when
        $listener = new PurgeHttpCacheListener(
            iriConverter: $this->iriConverterProphecy,
            resourceClassResolver: $this->resourceClassResolverProphecy,
            propertyAccessor: $this->propertyAccessorProphecy,
            resourceMetadataCollectionFactory: $this->metadataFactoryProphecy,
            cacheManager: $this->cacheManagerProphecy,
            em: $this->emProphecy,
        );
        $listener->onFlush();
        $listener->postFlush();
    }
}
