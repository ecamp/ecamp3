<?php

namespace App\Tests\Metadata\Resource\Factory;

use ApiPlatform\Api\FilterInterface;
use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use ApiPlatform\Metadata\Resource\ResourceNameCollection;
use ApiPlatform\State\Pagination\PaginationOptions;
use App\Metadata\Resource\Factory\UriTemplateFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @internal
 */
class UriTemplateFactoryTest extends TestCase {
    private UriTemplateFactory $uriTemplateFactory;
    private MockObject|ContainerInterface $filterLocator;
    private MockObject|ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory;
    private MockObject|ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory;
    private MockObject|IriConverterInterface $iriConverter;
    private PaginationOptions $paginationOptions;
    private ResourceNameCollection $resourceNameCollection;
    private ApiResource $apiResource;

    protected function setUp(): void {
        $this->filterLocator = $this->createMock(ContainerInterface::class);
        $this->resourceMetadataCollectionFactory = $this->createMock(ResourceMetadataCollectionFactoryInterface::class);
        $this->resourceNameCollectionFactory = $this->createMock(ResourceNameCollectionFactoryInterface::class);
        $this->iriConverter = $this->createMock(IriConverterInterface::class);
        $this->paginationOptions = new PaginationOptions(false);
        $this->resourceNameCollection = new ResourceNameCollection(['Dummy']);
        $this->resourceMetadataCollection = new ResourceMetadataCollection('Dummy');

        $this->apiResource = (new ApiResource())->withShortName('Dummy')->withOperations(new Operations([
            new Get(
                name: '_api_/dummys/{id}{._format}_get'
            ),
            new GetCollection(
                name: '_api_/dummys{._format}_get_collection'
            ),
        ]));

        $this->resourceNameCollectionFactory->method('create')->willReturnCallback(fn () => $this->resourceNameCollection);
        $this->resourceMetadataCollectionFactory->method('create')->with('Dummy')->willReturnCallback(fn () => $this->resourceMetadataCollection);
        $this->iriConverter->method('getIriFromResource')->willReturnCallback(function ($resourceClass) {
            return '/'.lcfirst($resourceClass).'s';
        });
    }

    public function testCantCreateUriTemplateForNonexistentResource() {
        // given
        $resource = 'Dummy';
        $this->resourceNameCollection = new ResourceNameCollection([]);
        $this->createFactory();

        // when
        [$uri, $templated] = $this->uriTemplateFactory->createFromShortname($resource);

        // then
        self::assertThat($uri, self::equalTo(null));
        self::assertThat($templated, self::isFalse());
    }

    public function testCreatesNonTemplatedUri() {
        // given
        $resource = 'Dummy';
        $operations = new Operations([
            new GetCollection(
                shortName: 'Dummy',
                name: '_api_/dummys{._format}_get_collection'
            ),
        ]);
        $apiResource = (new ApiResource())->withShortName('Dummy')->withOperations($operations);
        $this->resourceMetadataCollection->append($apiResource);

        $this->createFactory();

        // when
        [$uri, $templated] = $this->uriTemplateFactory->createFromShortname($resource);

        // then
        self::assertThat($uri, self::equalTo('/dummys'));
        self::assertThat($templated, self::isFalse());
    }

    public function testCreatesTemplatedUriWithIdPathParameter() {
        // given
        $resource = 'Dummy';
        $this->resourceMetadataCollection->append($this->apiResource);
        $this->createFactory();

        // when
        [$uri, $templated] = $this->uriTemplateFactory->createFromShortname($resource);

        // then
        self::assertThat($uri, self::equalTo('/dummys{/id}'));
        self::assertThat($templated, self::isTrue());
    }

    public function testCreatesTemplatedUriWithFilterQueryParameter() {
        // given
        $resource = 'Dummy';
        $this->resourceMetadataCollection->append((new ApiResource())->withShortName('Dummy')->withOperations(new Operations([
            new Get(
                name: '_api_/dummys/{id}{._format}_get'
            ),
            new GetCollection(
                name: '_api_/dummys{._format}_get_collection',
                filters: ['some_filter_identifier']
            ),
        ])));
        $filter = $this->createMock(FilterInterface::class);
        $filter->method('getDescription')->willReturn(['some_filter' => 'something']);
        $this->filterLocator->method('get')->with('some_filter_identifier')->willReturn($filter);
        $this->createFactory();

        // when
        [$uri, $templated] = $this->uriTemplateFactory->createFromShortname($resource);

        // then
        self::assertThat($uri, self::equalTo('/dummys{/id}{?some_filter}'));
        self::assertThat($templated, self::isTrue());
    }

    public function testCreatesTemplatedUriWithPaginationQueryParameter() {
        // given
        $resource = 'Dummy';
        $this->resourceMetadataCollection->append($this->apiResource);
        $this->paginationOptions = new PaginationOptions(true);
        $this->createFactory();

        // when
        [$uri, $templated] = $this->uriTemplateFactory->createFromShortname($resource);

        // then
        self::assertThat($uri, self::equalTo('/dummys{/id}{?page}'));
        self::assertThat($templated, self::isTrue());
    }

    public function testCreatesTemplatedUriWithAdvancedPaginationQueryParameters() {
        // given
        $resource = 'Dummy';
        $this->paginationOptions = new PaginationOptions(true);
        $this->resourceMetadataCollection->append((new ApiResource())->withShortName('Dummy')->withOperations(new Operations([
            new Get(
                name: '_api_/dummys/{id}{._format}_get'
            ),
            new GetCollection(
                name: '_api_/dummys{._format}_get_collection',
                paginationClientEnabled: true,
                paginationClientItemsPerPage: true
            ),
        ])));
        $this->createFactory();

        // when
        [$uri, $templated] = $this->uriTemplateFactory->createFromShortname($resource);

        // then
        self::assertThat($uri, self::equalTo('/dummys{/id}{?page,itemsPerPage,pagination}'));
        self::assertThat($templated, self::isTrue());
    }

    public function testCreatesTemplatedUriWithActionPathParameters() {
        // given
        $resource = 'Dummy';

        $this->resourceMetadataCollection->append((new ApiResource())->withShortName('Dummy')->withOperations(new Operations([
            new Get(
                name: '_api_/dummys/{id}{._format}_get'
            ),
            new Get(
                name: '_api_/dummys/{id}/find{._format}_get',
                uriTemplate: '/{inviteKey}/find{._format}',
            ),
            new Patch(
                name: '_api_/dummys/{id}/accept{._format}_get',
                uriTemplate: '/{inviteKey}/accept{._format}',
            ),
        ])));
        $this->createFactory();

        // when
        [$uri, $templated] = $this->uriTemplateFactory->createFromShortname($resource);

        // then
        self::assertThat($uri, self::equalTo('/dummys{/id}{/action}'));
        self::assertThat($templated, self::isTrue());
    }

    public function testIgnoresRoutesWithNoSlashAtEnd() {
        // given
        $resource = 'Dummy';
        $this->resourceMetadataCollection->append((new ApiResource())->withShortName('Dummy')->withOperations(new Operations([
            new Get(
                name: '_api_/dummys/{id}{._format}_get'
            ),
            new Get(
                uriTemplate: '/profiles{inviteKey}{._format}',
            ),
        ])));
        $this->createFactory();

        // when
        [$uri, $templated] = $this->uriTemplateFactory->createFromShortname($resource);

        // then
        self::assertThat($uri, self::equalTo('/dummys{/id}'));
        self::assertThat($templated, self::isTrue());
    }

    /**
     * This behaviour was not yet implemented, because we don't have the use case yet.
     *
     * @throws \ApiPlatform\Exception\ResourceClassNotFoundException
     */
    public function testDoesNotYetIgnoreActionPathsOfOtherRouteStarts() {
        // given
        $resource = 'Dummy';
        $this->resourceMetadataCollection->append((new ApiResource())->withShortName('Dummy')->withOperations(new Operations([
            new Get(
                name: '_api_/dummys/{id}{._format}_get'
            ),
            new Get(
                uriTemplate: '/profiles{/id}/ignoreThis{._format}',
            ),
        ])));
        $this->createFactory();

        // when
        [$uri, $templated] = $this->uriTemplateFactory->createFromShortname($resource);

        // then
        self::assertThat($uri, self::equalTo('/dummys{/id}{/action}'));
        self::assertThat($templated, self::isTrue());
    }

    protected function createFactory() {
        $this->uriTemplateFactory = new UriTemplateFactory(
            $this->filterLocator,
            $this->resourceMetadataCollectionFactory,
            $this->resourceNameCollectionFactory,
            $this->iriConverter,
            $this->paginationOptions,
        );
    }
}
