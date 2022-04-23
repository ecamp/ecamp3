<?php

namespace App\Tests\Metadata\Resource\Factory;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Api\FilterInterface;
use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataProvider\PaginationOptions;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Metadata\Resource\ResourceNameCollection;
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
    private MockObject|ResourceMetadataFactoryInterface $resourceMetadataFactory;
    private MockObject|ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory;
    private MockObject|IriConverterInterface $iriConverter;
    private PaginationOptions $paginationOptions;
    private ResourceNameCollection $resourceNameCollection;
    private ResourceMetadata $resourceMetadata;

    protected function setUp(): void {
        $this->filterLocator = $this->createMock(ContainerInterface::class);
        $this->resourceMetadataFactory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $this->resourceNameCollectionFactory = $this->createMock(ResourceNameCollectionFactoryInterface::class);
        $this->iriConverter = $this->createMock(IriConverterInterface::class);
        $this->paginationOptions = new PaginationOptions(false);
        $this->resourceNameCollection = new ResourceNameCollection(['Dummy']);
        $this->resourceMetadata = new ResourceMetadata('Dummy', null, null, ['get' => []]);

        $this->resourceNameCollectionFactory->method('create')->willReturnCallback(fn () => $this->resourceNameCollection);
        $this->resourceMetadataFactory->method('create')->with('Dummy')->willReturnCallback(fn () => $this->resourceMetadata);
        $this->iriConverter->method('getIriFromResourceClass')->willReturnCallback(function ($resourceClass) {
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
        $this->resourceMetadata = $this->resourceMetadata->withItemOperations([
            'get' => ['controller' => NotFoundAction::class],
            'patch' => [],
        ]);
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
        $this->resourceMetadata = $this->resourceMetadata->withAttributes([
            'filters' => ['some_filter_identifier'],
        ]);
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
        $this->resourceMetadata = $this->resourceMetadata->withAttributes([
            'pagination_client_items_per_page' => true,
            'pagination_client_enabled' => true,
        ]);
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
        $this->resourceMetadata = new ResourceMetadata(
            'Dummy',
            null,
            null,
            [
                'get' => [],
                'find' => [
                    'path' => '{/inviteKey}/find',
                ],
                'accept' => [
                    'path' => '/{inviteKey}/accept',
                ],
            ]
        );
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
        $this->resourceMetadata = new ResourceMetadata(
            'Dummy',
            null,
            null,
            [
                'get' => [],
                'profiles1' => [
                    'path' => 'profiles{/id}',
                ],
            ]
        );
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
     * @throws \ApiPlatform\Core\Exception\ResourceClassNotFoundException
     */
    public function testDoesNotYetIgnoreActionPathsOfOtherRouteStarts() {
        // given
        $resource = 'Dummy';
        $this->resourceMetadata = new ResourceMetadata(
            'Dummy',
            null,
            null,
            [
                'get' => [],
                'profiles1' => [
                    'path' => 'profiles{/id}/ignoreThis',
                ],
            ],
        );
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
            $this->resourceMetadataFactory,
            $this->resourceNameCollectionFactory,
            $this->iriConverter,
            $this->paginationOptions,
        );
    }
}
