<?php

namespace App\Tests\Metadata\Resource\Factory;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use App\Metadata\Resource\Factory\GraphQLSecurityResourceMetadataFactory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class GraphQLSecurityResourceMetadataFactoryTest extends TestCase {
    public function testCopiesRestSecurity() {
        // given
        $decoratedFactoryMock = $this->createMock(ResourceMetadataFactoryInterface::class);
        $factory = new GraphQLSecurityResourceMetadataFactory($decoratedFactoryMock);
        $decoratedFactoryMock->expects($this->once())->method('create')->willReturn(new ResourceMetadata(
            null,
            null,
            null,
            [
                'get' => ['security' => 'item_get_pre', 'security_post_denormalize' => 'item_get_post'],
                'patch' => ['security' => 'item_patch_pre', 'security_post_denormalize' => 'item_patch_post'],
                'delete' => ['security' => 'item_delete_pre', 'security_post_denormalize' => 'item_delete_post'],
            ],
            [
                'get' => ['security' => 'collection_get_pre', 'security_post_denormalize' => 'collection_get_post'],
                'post' => ['security' => 'collection_post_pre', 'security_post_denormalize' => 'collection_post_post'],
            ]
        ));

        // when
        $metadata = $factory->create('MyResourceClass');

        // then
        $this->assertEquals([
            'collection_query' => [
                'security' => 'collection_get_pre',
                'security_post_denormalize' => 'collection_get_post',
            ],
            'item_query' => [
                'security' => 'item_get_pre',
                'security_post_denormalize' => 'item_get_post',
            ],
            'create' => [
                'security' => 'collection_post_pre',
                'security_post_denormalize' => 'collection_post_post',
            ],
            'update' => [
                'security' => 'item_patch_pre',
                'security_post_denormalize' => 'item_patch_post',
            ],
            'delete' => [
                'security' => 'item_delete_pre',
                'security_post_denormalize' => 'item_delete_post',
            ],
        ], $metadata->getGraphql());
    }

    public function testPrefersExplicitGraphQLRules() {
        // given
        $decoratedFactoryMock = $this->createMock(ResourceMetadataFactoryInterface::class);
        $factory = new GraphQLSecurityResourceMetadataFactory($decoratedFactoryMock);
        $decoratedFactoryMock->expects($this->once())->method('create')->willReturn(new ResourceMetadata(
            null,
            null,
            null,
            [
                'get' => ['security' => 'item_get_pre', 'security_post_denormalize' => 'item_get_post'],
                'patch' => ['security' => 'item_patch_pre', 'security_post_denormalize' => 'item_patch_post'],
                'delete' => ['security' => 'item_delete_pre', 'security_post_denormalize' => 'item_delete_post'],
            ],
            [
                'get' => ['security' => 'collection_get_pre', 'security_post_denormalize' => 'collection_get_post'],
                'post' => ['security' => 'collection_post_pre', 'security_post_denormalize' => 'collection_post_post'],
            ],
            null,
            null,
            [
                'collection_query' => ['security' => 'collection_query_pre', 'security_post_denormalize' => 'collection_query_post'],
                'item_query' => ['security' => 'item_query_pre', 'security_post_denormalize' => 'item_query_post'],
                'create' => ['security' => 'create_pre', 'security_post_denormalize' => 'create_post'],
                'update' => ['security' => 'update_pre', 'security_post_denormalize' => 'update_post'],
                'delete' => ['security' => 'delete_pre', 'security_post_denormalize' => 'delete_post'],
            ]
        ));

        // when
        $metadata = $factory->create('MyResourceClass');

        // then
        $this->assertEquals([
            'collection_query' => [
                'security' => 'collection_query_pre',
                'security_post_denormalize' => 'collection_query_post',
            ],
            'item_query' => [
                'security' => 'item_query_pre',
                'security_post_denormalize' => 'item_query_post',
            ],
            'create' => [
                'security' => 'create_pre',
                'security_post_denormalize' => 'create_post',
            ],
            'update' => [
                'security' => 'update_pre',
                'security_post_denormalize' => 'update_post',
            ],
            'delete' => [
                'security' => 'delete_pre',
                'security_post_denormalize' => 'delete_post',
            ],
        ], $metadata->getGraphql());
    }

    public function testCopiesNonstandardOperationSecurity() {
        // given
        $decoratedFactoryMock = $this->createMock(ResourceMetadataFactoryInterface::class);
        $factory = new GraphQLSecurityResourceMetadataFactory($decoratedFactoryMock);
        $decoratedFactoryMock->expects($this->once())->method('create')->willReturn(new ResourceMetadata(
            null,
            null,
            null,
            [
                'accept' => ['security' => 'item_accept_pre', 'security_post_denormalize' => 'item_accept_post'],
            ],
            [
                'delete_all' => ['security' => 'collection_delete_all_pre', 'security_post_denormalize' => 'collection_delete_all_post'],
            ]
        ));

        // when
        $metadata = $factory->create('MyResourceClass');

        // then
        $this->assertEquals([
            'accept' => [
                'security' => 'item_accept_pre',
                'security_post_denormalize' => 'item_accept_post',
            ],
            'delete_all' => [
                'security' => 'collection_delete_all_pre',
                'security_post_denormalize' => 'collection_delete_all_post',
            ],
        ], $metadata->getGraphql());
    }

    public function testDoesntCopyRestOperationsWithoutSecurity() {
        // given
        $decoratedFactoryMock = $this->createMock(ResourceMetadataFactoryInterface::class);
        $factory = new GraphQLSecurityResourceMetadataFactory($decoratedFactoryMock);
        $decoratedFactoryMock->expects($this->once())->method('create')->willReturn(new ResourceMetadata(
            null,
            null,
            null,
            [
                'get' => [],
                'patch' => ['security' => 'item_patch_pre', 'security_post_denormalize' => 'item_patch_post'],
                'delete' => [],
            ],
            [
                'get' => ['security' => 'collection_get_pre', 'security_post_denormalize' => 'collection_get_post'],
                'post' => [],
            ]
        ));

        // when
        $metadata = $factory->create('MyResourceClass');

        // then
        $this->assertEquals([
            'collection_query' => [
                'security' => 'collection_get_pre',
                'security_post_denormalize' => 'collection_get_post',
            ],
            'update' => [
                'security' => 'item_patch_pre',
                'security_post_denormalize' => 'item_patch_post',
            ],
        ], $metadata->getGraphql());
    }
}
