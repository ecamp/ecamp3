<?php

/*
 * Tests copied from api-platform/core and adapted to our modified PublishMercureUpdatesListener
 * (relative IRIs as topics, private updates by default, JSON Patch payloads).
 *
 * Upstream file (last synchronized version): https://github.com/api-platform/core/blob/23840f9df42895d334d0db1027e3894ce411cf98/src/Symfony/Tests/Doctrine/EventListener/PublishMercureUpdatesListenerTest.php
 */

namespace App\Tests\Mercure;

use ApiPlatform\GraphQl\Subscription\MercureSubscriptionIriGeneratorInterface as GraphQlMercureSubscriptionIriGeneratorInterface;
use ApiPlatform\GraphQl\Subscription\SubscriptionManagerInterface as GraphQlSubscriptionManagerInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use ApiPlatform\Metadata\ResourceClassResolverInterface;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use App\Mercure\PublishMercureUpdatesListener;
use App\Tests\Mercure\Fixtures\Dummy;
use App\Tests\Mercure\Fixtures\DummyCar;
use App\Tests\Mercure\Fixtures\DummyFriend;
use App\Tests\Mercure\Fixtures\DummyMercure;
use App\Tests\Mercure\Fixtures\DummyMercureMultiResource;
use App\Tests\Mercure\Fixtures\DummyOffer;
use App\Tests\Mercure\Fixtures\MercureWithTopicsAndGetOperation;
use App\Tests\Mercure\Fixtures\NotAResource;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\HubRegistry;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Symfony\Component\Mercure\MockHub;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
class PublishMercureUpdatesListenerTest extends TestCase {
    private const RESOURCE_CLASSES = [
        Dummy::class,
        DummyCar::class,
        DummyFriend::class,
        DummyOffer::class,
        DummyMercure::class,
        MercureWithTopicsAndGetOperation::class,
        DummyMercureMultiResource::class,
    ];

    public function testPublishUpdate(): void {
        $toInsert = $this->fixture(Dummy::class, 1);
        $toInsertNotResource = new NotAResource();

        $toUpdate = $this->fixture(Dummy::class, 2);
        $toUpdateNoMercureAttribute = $this->fixture(DummyCar::class, 6);
        $toUpdateMercureOptions = $this->fixture(DummyOffer::class, 7);
        $toUpdateMercureTopicOptions = $this->fixture(DummyMercure::class, 8);

        $toDelete = $this->fixture(Dummy::class, 3);
        $toDeleteExpressionLanguage = $this->fixture(DummyFriend::class, 4);
        $toDeleteMercureOptions = $this->fixture(DummyOffer::class, 5);

        $iris = [
            spl_object_id($toInsert) => '/dummies/1',
            spl_object_id($toUpdate) => '/dummies/2',
            spl_object_id($toUpdateMercureOptions) => '/dummy_offers/7',
            spl_object_id($toUpdateMercureTopicOptions) => '/dummy_mercures/8',
            spl_object_id($toDelete) => '/dummies/3',
            spl_object_id($toDeleteExpressionLanguage) => '/dummy_friends/4',
            spl_object_id($toDeleteMercureOptions) => '/dummy_offers/5',
        ];

        $metadata = [
            Dummy::class => new ResourceMetadataCollection(Dummy::class, [(new ApiResource())->withOperations(new Operations([
                'get' => (new Get())->withShortName('Dummy')->withMercure(['hub' => 'managed', 'enable_async_update' => false])->withNormalizationContext(['groups' => ['foo', 'bar']]),
            ]))]),
            DummyCar::class => new ResourceMetadataCollection(DummyCar::class, [(new ApiResource())->withOperations(new Operations([
                'get' => new Get(),
            ]))]),
            DummyFriend::class => new ResourceMetadataCollection(DummyFriend::class, [(new ApiResource())->withOperations(new Operations([
                'get' => (new Get())->withTypes('https://schema.org/Person')->withShortName('DummyFriend')->withMercure(['private' => true, 'retry' => 10, 'hub' => 'managed', 'enable_async_update' => false]),
            ]))]),
            DummyOffer::class => new ResourceMetadataCollection(DummyOffer::class, [(new ApiResource())->withOperations(new Operations([
                'get' => (new Get())->withShortName('DummyOffer')->withMercure(['topics' => 'http://example.com/custom_topics/1', 'data' => 'mercure_custom_data', 'hub' => 'managed', 'enable_async_update' => false])->withNormalizationContext(['groups' => ['baz']]),
            ]))]),
            DummyMercure::class => new ResourceMetadataCollection(DummyMercure::class, [(new ApiResource())->withOperations(new Operations([
                'get' => (new Get())->withMercure(['topics' => ['/dummies/1', '/users/3'], 'private' => false, 'hub' => 'managed', 'enable_async_update' => false])->withNormalizationContext(['groups' => ['baz']]),
            ]))]),
        ];

        $serialized = [
            spl_object_id($toInsert) => '1',
            spl_object_id($toUpdate) => '2',
            spl_object_id($toUpdateMercureTopicOptions) => 'mercure_options',
        ];
        $serializer = $this->createStub(SerializerInterface::class);
        $serializer->method('serialize')->willReturnCallback(
            fn (object $object, string $format, array $context) => $serialized[spl_object_id($object)] ?? $this->fail('Unexpected serialization of '.$object::class)
        );

        $published = [];
        $managedHub = $this->createMockHub($this->collectUpdates($published));

        $listener = new PublishMercureUpdatesListener(
            $this->createResourceClassResolver(),
            $this->createIriConverter(fn (object $object, int $referenceType, ?Operation $operation) => $iris[spl_object_id($object)]),
            $this->createMetadataFactory($metadata),
            $serializer,
            ['jsonld' => ['application/ld+json'], 'jsonhal' => ['application/hal+json']],
            null,
            new HubRegistry($this->createStub(HubInterface::class), ['managed' => $managedHub]),
            null,
            null,
            null,
            true,
        );

        $this->flush(
            $listener,
            [$toInsert, $toInsertNotResource],
            [$toUpdate, $toUpdateNoMercureAttribute, $toUpdateMercureOptions, $toUpdateMercureTopicOptions],
            [$toDelete, $toDeleteExpressionLanguage, $toDeleteMercureOptions]
        );

        $this->assertEquals([
            self::patch('add', '/dummies/1', '1'),
            self::patch('replace', '/dummies/2', '2'),
            'mercure_custom_data',
            self::patch('replace', '/dummy_mercures/8', 'mercure_options'),
            self::remove('/dummies/3'),
            self::remove('/dummy_friends/4'),
            self::remove('/dummy_offers/5'),
        ], array_column($published, 'data'));
        $this->assertEquals([
            ['/dummies/1'],
            ['/dummies/2'],
            ['http://example.com/custom_topics/1'],
            ['/dummies/1', '/users/3'],
            ['/dummies/3'],
            ['/dummy_friends/4'],
            ['http://example.com/custom_topics/1'],
        ], array_column($published, 'topics'));
        $this->assertEquals([true, true, true, false, true, true, true], array_column($published, 'private'));
        $this->assertEquals([null, null, null, null, null, 10, null], array_column($published, 'retry'));
    }

    public function testPublishUpdateMultipleTopicsUsingExpressionLanguage(): void {
        $mercure = [
            'topics' => [
                '@=iri(object)',
                '@=iri(object, '.UrlGeneratorInterface::ABS_PATH.')',
                '@=iri(object, '.UrlGeneratorInterface::ABS_URL.', get_operation(object, "/custom_resource/mercure_with_topics_and_get_operations/{id}{._format}"))',
            ],
        ];

        $toInsert = $this->fixture(MercureWithTopicsAndGetOperation::class, 1);
        $toUpdate = $this->fixture(MercureWithTopicsAndGetOperation::class, 2);
        $toDelete = $this->fixture(MercureWithTopicsAndGetOperation::class, 3);

        // Even if it's the Post operation which sends Updates to Mercure,
        // the `mercure` configuration is retrieved from the first operation
        // of the resource because the Doctrine Listener doesn't have a
        // reference to the operation.
        $getOperation = (new Get())->withMercure($mercure)->withShortName('MercureWithTopicsAndGetOperation');
        $customGetOperation = new Get(uriTemplate: '/custom_resource/mercure_with_topics_and_get_operations/{id}{._format}');
        $postOperation = new Post();

        $metadata = [
            MercureWithTopicsAndGetOperation::class => new ResourceMetadataCollection(MercureWithTopicsAndGetOperation::class, [
                (new ApiResource())->withOperations(new Operations([
                    'get' => $getOperation,
                    'custom_get' => $customGetOperation,
                    'post' => $postOperation,
                ])),
            ]),
        ];

        $iriConverter = $this->createIriConverter(function (object $object, int $referenceType, ?Operation $operation) use ($customGetOperation) {
            $prefix = UrlGeneratorInterface::ABS_URL === $referenceType ? 'http://example.com' : '';
            $custom = $operation === $customGetOperation ? '/custom_resource' : '';

            return $prefix.$custom.'/mercure_with_topics_and_get_operations/'.$object->id;
        });

        $serializer = $this->createStub(SerializerInterface::class);
        $serializer->method('serialize')->willReturnCallback(
            static fn (object $object) => '{"@type":"MercureWithTopicsAndGetOperation","id":'.$object->id.',"name":"Hello World!"}'
        );

        $published = [];
        $defaultHub = $this->createMockHub($this->collectUpdates($published));

        $listener = new PublishMercureUpdatesListener(
            $this->createResourceClassResolver(),
            $iriConverter,
            $this->createMetadataFactory($metadata),
            $serializer,
            ['jsonld' => ['application/ld+json'], 'jsonhal' => ['application/hal+json']],
            null,
            new HubRegistry($defaultHub, ['default' => $defaultHub]),
            null,
            null,
            null,
            true,
        );

        $this->flush($listener, [$toInsert], [$toUpdate], [$toDelete]);

        $this->assertEquals([
            self::patch('add', '/mercure_with_topics_and_get_operations/1', '{"@type":"MercureWithTopicsAndGetOperation","id":1,"name":"Hello World!"}'),
            self::patch('replace', '/mercure_with_topics_and_get_operations/2', '{"@type":"MercureWithTopicsAndGetOperation","id":2,"name":"Hello World!"}'),
            self::remove('/mercure_with_topics_and_get_operations/3'),
        ], array_column($published, 'data'));
        // The default reference type of the iri() expression function is ABS_PATH
        $this->assertEquals([
            ['/mercure_with_topics_and_get_operations/1', '/mercure_with_topics_and_get_operations/1', 'http://example.com/custom_resource/mercure_with_topics_and_get_operations/1'],
            ['/mercure_with_topics_and_get_operations/2', '/mercure_with_topics_and_get_operations/2', 'http://example.com/custom_resource/mercure_with_topics_and_get_operations/2'],
            ['/mercure_with_topics_and_get_operations/3', '/mercure_with_topics_and_get_operations/3', 'http://example.com/custom_resource/mercure_with_topics_and_get_operations/3'],
        ], array_column($published, 'topics'));
    }

    public function testPublishGraphQlUpdates(): void {
        $toUpdate = $this->fixture(Dummy::class, 2);

        $metadata = [
            Dummy::class => new ResourceMetadataCollection(Dummy::class, [(new ApiResource())->withOperations(new Operations([
                'get' => (new Get())->withMercure(['enable_async_update' => false])->withNormalizationContext(['groups' => ['foo', 'bar']]),
            ]))]),
        ];

        $serializer = $this->createStub(SerializerInterface::class);
        $serializer->method('serialize')->willReturn('2');

        $published = [];
        $defaultHub = $this->createMockHub($this->collectUpdates($published));

        $graphQlSubscriptionId = 'subscription-id';
        $graphQlSubscriptionManager = $this->createStub(GraphQlSubscriptionManagerInterface::class);
        $graphQlSubscriptionManager->method('getPushPayloads')->willReturn([[$graphQlSubscriptionId, ['data']]]);
        $graphQlMercureSubscriptionIriGenerator = $this->createStub(GraphQlMercureSubscriptionIriGeneratorInterface::class);
        $graphQlMercureSubscriptionIriGenerator->method('generateTopicIri')->willReturnCallback(
            fn (string $subscriptionId) => $graphQlSubscriptionId === $subscriptionId ? 'subscription-topic-iri' : $this->fail('Unexpected subscription id')
        );

        $listener = new PublishMercureUpdatesListener(
            $this->createResourceClassResolver(),
            $this->createIriConverter(fn () => '/dummies/2'),
            $this->createMetadataFactory($metadata),
            $serializer,
            ['jsonld' => ['application/ld+json'], 'jsonhal' => ['application/hal+json']],
            null,
            new HubRegistry($defaultHub, ['default' => $defaultHub]),
            $graphQlSubscriptionManager,
            $graphQlMercureSubscriptionIriGenerator,
            null,
            true,
        );

        $this->flush($listener, [], [$toUpdate], []);

        $this->assertEquals([['/dummies/2'], ['subscription-topic-iri']], array_column($published, 'topics'));
        $this->assertEquals([true, true], array_column($published, 'private'));
        $this->assertEquals([null, null], array_column($published, 'retry'));
        $this->assertEquals([self::patch('replace', '/dummies/2', '2'), '["data"]'], array_column($published, 'data'));
    }

    public function testPublishUpdateWithMultipleResources(): void {
        $toInsert = $this->fixture(DummyMercureMultiResource::class, 1);
        $toDelete = $this->fixture(DummyMercureMultiResource::class, 2);

        $adminGetOp = (new Get(uriTemplate: '/admin/dummy_mercures/{id}{._format}'))->withShortName('AdminDummyMercure')->withMercure(['enable_async_update' => false, 'hub' => 'managed'])->withNormalizationContext(['groups' => ['admin:read']]);
        $publicGetOp = (new Get(uriTemplate: '/dummy_mercures/{id}{._format}'))->withShortName('DummyMercure')->withMercure(['enable_async_update' => false, 'hub' => 'managed'])->withNormalizationContext(['groups' => ['read']]);

        $metadata = [
            DummyMercureMultiResource::class => new ResourceMetadataCollection(DummyMercureMultiResource::class, [
                (new ApiResource())->withShortName('AdminDummyMercure')->withOperations(new Operations(['get' => $adminGetOp])),
                (new ApiResource())->withShortName('DummyMercure')->withOperations(new Operations(['get' => $publicGetOp])),
            ]),
        ];

        $iriConverter = $this->createIriConverter(
            static fn (object $object, int $referenceType, ?Operation $operation) => ($operation === $adminGetOp ? '/admin' : '').'/dummy_mercures/'.$object->id
        );

        $serializer = $this->createStub(SerializerInterface::class);
        $serializer->method('serialize')->willReturnCallback(
            static fn (object $object, string $format, array $context) => in_array('admin:read', $context['groups'], true) ? '{"admin":1}' : '{"public":1}'
        );

        $published = [];
        $managedHub = $this->createMockHub($this->collectUpdates($published));

        $listener = new PublishMercureUpdatesListener(
            $this->createResourceClassResolver(),
            $iriConverter,
            $this->createMetadataFactory($metadata),
            $serializer,
            ['jsonld' => ['application/ld+json']],
            null,
            new HubRegistry($this->createStub(HubInterface::class), ['managed' => $managedHub]),
            null,
            null,
            null,
            true,
        );

        $this->flush($listener, [$toInsert], [], [$toDelete]);

        // Both resources should have published updates
        $this->assertCount(4, $published, 'Expected 4 updates: 2 inserts (admin + public) + 2 deletes (admin + public)');
        $this->assertEquals([
            self::patch('add', '/admin/dummy_mercures/1', '{"admin":1}'),
            self::patch('add', '/dummy_mercures/1', '{"public":1}'),
            self::remove('/admin/dummy_mercures/2'),
            self::remove('/dummy_mercures/2'),
        ], array_column($published, 'data'));
        $this->assertEquals([['/admin/dummy_mercures/1'], ['/dummy_mercures/1'], ['/admin/dummy_mercures/2'], ['/dummy_mercures/2']], array_column($published, 'topics'));
    }

    public function testResetDiscardsCollectedObjects(): void {
        $toInsert = $this->fixture(Dummy::class, 1);

        $metadata = [
            Dummy::class => new ResourceMetadataCollection(Dummy::class, [(new ApiResource())->withOperations(new Operations([
                'get' => (new Get())->withMercure(['enable_async_update' => false]),
            ]))]),
        ];

        $published = [];
        $defaultHub = $this->createMockHub($this->collectUpdates($published));

        $listener = new PublishMercureUpdatesListener(
            $this->createResourceClassResolver(),
            $this->createIriConverter(fn () => '/dummies/1'),
            $this->createMetadataFactory($metadata),
            $this->createStub(SerializerInterface::class),
            ['jsonld' => ['application/ld+json']],
            null,
            new HubRegistry($defaultHub, ['default' => $defaultHub]),
        );

        $listener->onFlush($this->createOnFlushEventArgs([$toInsert], [], []));
        $listener->reset();
        $listener->postFlush();

        $this->assertSame([], $published);
    }

    public function testConstructorRequiresMessageBusOrHubRegistry(): void {
        $this->expectException(InvalidArgumentException::class);

        new PublishMercureUpdatesListener(
            $this->createStub(ResourceClassResolverInterface::class),
            $this->createStub(IriConverterInterface::class),
            $this->createStub(ResourceMetadataCollectionFactoryInterface::class),
            $this->createStub(SerializerInterface::class),
            ['jsonld' => ['application/ld+json']],
        );
    }

    public function testUnknownMercureOptionThrows(): void {
        $metadata = [
            Dummy::class => new ResourceMetadataCollection(Dummy::class, [(new ApiResource())->withOperations(new Operations([
                'get' => (new Get())->withMercure(['unknown' => true]),
            ]))]),
        ];

        $defaultHub = $this->createMockHub(static fn () => 'id');
        $listener = new PublishMercureUpdatesListener(
            $this->createResourceClassResolver(),
            $this->createIriConverter(fn () => '/dummies/1'),
            $this->createMetadataFactory($metadata),
            $this->createStub(SerializerInterface::class),
            ['jsonld' => ['application/ld+json']],
            null,
            new HubRegistry($defaultHub, ['default' => $defaultHub]),
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The option "unknown" set in the "mercure" attribute');

        $listener->onFlush($this->createOnFlushEventArgs([$this->fixture(Dummy::class, 1)], [], []));
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    private function fixture(string $class, int $id): object {
        $object = new $class();
        $object->id = $id;

        return $object;
    }

    private static function patch(string $op, string $path, string $value): string {
        return json_encode(['op' => $op, 'path' => $path, 'value' => $value]);
    }

    private static function remove(string $path): string {
        return json_encode(['op' => 'remove', 'path' => $path, 'value' => null]);
    }

    private function createResourceClassResolver(): ResourceClassResolverInterface {
        $resolver = $this->createStub(ResourceClassResolverInterface::class);
        $resolver->method('isResourceClass')->willReturnCallback(
            static fn (string $class) => in_array($class, self::RESOURCE_CLASSES, true)
        );
        $resolver->method('getResourceClass')->willReturnCallback(
            static fn (object $object) => $object::class
        );

        return $resolver;
    }

    /**
     * @param callable(object, int, ?Operation): string $iriFor
     */
    private function createIriConverter(callable $iriFor): IriConverterInterface {
        $iriConverter = $this->createStub(IriConverterInterface::class);
        $iriConverter->method('getIriFromResource')->willReturnCallback(
            static fn (object|string $resource, int $referenceType = UrlGeneratorInterface::ABS_PATH, ?Operation $operation = null) => $iriFor($resource, $referenceType, $operation)
        );

        return $iriConverter;
    }

    /**
     * @param array<class-string, ResourceMetadataCollection> $metadata
     */
    private function createMetadataFactory(array $metadata): ResourceMetadataCollectionFactoryInterface {
        $factory = $this->createStub(ResourceMetadataCollectionFactoryInterface::class);
        $factory->method('create')->willReturnCallback(
            static fn (string $class) => $metadata[$class]
        );

        return $factory;
    }

    /**
     * @param list<object> $insertions
     * @param list<object> $updates
     * @param list<object> $deletions
     */
    private function createOnFlushEventArgs(array $insertions, array $updates, array $deletions): OnFlushEventArgs {
        $uow = $this->createStub(UnitOfWork::class);
        $uow->method('getScheduledEntityInsertions')->willReturn($insertions);
        $uow->method('getScheduledEntityUpdates')->willReturn($updates);
        $uow->method('getScheduledEntityDeletions')->willReturn($deletions);

        $em = $this->createStub(EntityManagerInterface::class);
        $em->method('getUnitOfWork')->willReturn($uow);

        return new OnFlushEventArgs($em);
    }

    private function flush(PublishMercureUpdatesListener $listener, array $insertions, array $updates, array $deletions): void {
        $listener->onFlush($this->createOnFlushEventArgs($insertions, $updates, $deletions));
        $listener->postFlush();
    }

    /**
     * @param list<array{topics: string[], private: bool, retry: ?int, data: string}> $published
     */
    private function collectUpdates(array &$published): callable {
        return static function (Update $update) use (&$published): string {
            $published[] = [
                'topics' => $update->getTopics(),
                'private' => $update->isPrivate(),
                'retry' => $update->getRetry(),
                'data' => $update->getData(),
            ];

            return 'id';
        };
    }

    private function createMockHub(callable $callable): HubInterface {
        return new MockHub('https://mercure.demo/.well-known/mercure', new StaticTokenProvider('x'), $callable);
    }
}
