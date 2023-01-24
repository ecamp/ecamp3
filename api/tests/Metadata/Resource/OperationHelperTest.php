<?php

namespace App\Tests\Metadata\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use App\Metadata\Resource\OperationHelper;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class OperationHelperTest extends TestCase {
    private ResourceMetadataCollection $resourceMetadataCollection;

    protected function setUp(): void {
        $this->resourceMetadataCollection = new ResourceMetadataCollection('Dummy');
    }

    public function testFindGet() {
        // given
        $apiResource = (new ApiResource())->withOperations(new Operations([
            new Get(
                name: 'get'
            ),
            new GetCollection(
                name: 'get_collection'
            ),
        ]));
        $this->resourceMetadataCollection->append($apiResource);

        // when
        $operation = OperationHelper::findOneByType($this->resourceMetadataCollection, Get::class);

        // then
        $this->assertEquals('get', $operation->getName());
    }

    public function testReturnsNullForMissingOperation() {
        // given
        $apiResource = (new ApiResource())->withOperations(new Operations([
            new Get(
                name: 'get'
            ),
        ]));

        $this->resourceMetadataCollection->append($apiResource);

        // when
        $operation = OperationHelper::findOneByType($this->resourceMetadataCollection, Patch::class);

        // then
        $this->assertNull($operation);
    }

    public function testFindsFirstOperation() {
        // given
        $apiResource = (new ApiResource())->withOperations(new Operations([
            new Get(
                name: 'get1'
            ),
            new Get(
                name: 'get2'
            ),
        ]));
        $this->resourceMetadataCollection->append($apiResource);

        // when
        $operation = OperationHelper::findOneByType($this->resourceMetadataCollection, Get::class);

        // then
        $this->assertEquals('get1', $operation->getName());
    }

    public function testFindsOperationInMultipleResources() {
        // given
        $apiResource1 = (new ApiResource())->withOperations(new Operations([
            new GetCollection(
                name: 'get_collection'
            ),
        ]));
        $apiResource2 = (new ApiResource())->withOperations(new Operations([
            new Get(
                name: 'get1'
            ),
            new Get(
                name: 'get2'
            ),
        ]));
        $this->resourceMetadataCollection->append($apiResource1);
        $this->resourceMetadataCollection->append($apiResource2);

        // when
        $operation = OperationHelper::findOneByType($this->resourceMetadataCollection, Get::class);

        // then
        $this->assertEquals('get1', $operation->getName());
    }

    public function testFindGraphQlOperation() {
        // given
        $apiResource = (new ApiResource())->withGraphQlOperations([
            new Query(name: 'query'),
            new QueryCollection(name: 'queryCollection'),
        ]);
        $this->resourceMetadataCollection->append($apiResource);

        // when
        $operation = OperationHelper::findOneByType($this->resourceMetadataCollection, QueryCollection::class);

        // then
        $this->assertEquals('queryCollection', $operation->getName());
    }
}
