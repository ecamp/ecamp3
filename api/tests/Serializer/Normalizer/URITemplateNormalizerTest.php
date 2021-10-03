<?php

namespace App\Tests\Serializer\Normalizer;

use ApiPlatform\Core\Api\Entrypoint;
use ApiPlatform\Core\Api\UrlGeneratorInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\Metadata\Resource\ResourceNameCollection;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Info;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Paths;
use ApiPlatform\Core\OpenApi\OpenApi;
use App\Entity\Activity;
use App\Entity\Camp;
use App\Serializer\Normalizer\URITemplateNormalizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

/**
 * @internal
 */
class URITemplateNormalizerTest extends TestCase {
    private Operation $OPERATION_WITHOUT_PARAMETER;
    private Operation $PATH_PARAMETER;
    private Operation $QUERY_PARAMETER;
    private Operation $PATH_AND_QUERY_PARAMETER;

    private MockObject|NormalizerInterface $decorated;
    private MockObject|URITemplateNormalizer $uriTemplateNormalizer;
    private Paths $paths;
    private MockObject|ResourceMetadataFactoryInterface $resourceMetadataFactory;

    protected function setUp(): void {
        $idInPathParameter = new Parameter(name: 'id', in: 'path');
        $campQueryParameter = new Parameter(name: 'camp', in: 'query');
        $campArrayQueryParameter = new Parameter(name: 'camp[]', in: 'query');

        $this->OPERATION_WITHOUT_PARAMETER = new Operation(tags: ['camp']);
        $this->PATH_PARAMETER = new Operation(tags: ['camp'], parameters: [$idInPathParameter]);
        $this->QUERY_PARAMETER = new Operation(tags: ['activity'], parameters: [$campQueryParameter, $campArrayQueryParameter]);
        $this->PATH_AND_QUERY_PARAMETER = new Operation(tags: ['activity'], parameters: [$idInPathParameter, $campQueryParameter, $campArrayQueryParameter]);

        $this->decorated = $this->createMock(NormalizerInterface::class);
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturnCallback(function (string $arg) {
            if ('api_entrypoint' === $arg) {
                return '/';
            }

            return null;
        });

        $openApiFactory = $this->createMock(OpenApiFactoryInterface::class);
        $this->resourceMetadataFactory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $englishInflector = new EnglishInflector();
        $this->uriTemplateNormalizer = new URITemplateNormalizer(
            $this->decorated,
            $this->resourceMetadataFactory,
            $urlGenerator,
            $openApiFactory,
            $englishInflector
        );

        $this->paths = new Paths();

        $openApi = new OpenApi(info: new Info('', ''), servers: [], paths: $this->paths);
        $openApiFactory->method('__invoke')->willReturn($openApi);
    }

    /**
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testCreateNotTemplatedLinkIfNoParameters() {
        $this->paths->addPath('/camps', new PathItem(get: $this->OPERATION_WITHOUT_PARAMETER));
        $resource = new Entrypoint(new ResourceNameCollection([Camp::class]));
        $this->resourceMetadataFactory->expects($this->once())
            ->method('create')
            ->willReturn(new ResourceMetadata('camp', collectionOperations: [$this->OPERATION_WITHOUT_PARAMETER]))
        ;

        $normalize = $this->uriTemplateNormalizer->normalize($resource);

        self::assertThat($normalize, self::equalTo(
            [
                '_links' => [
                    'self' => [
                        'href' => '/',
                    ],
                    'auth' => [
                        'href' => '/authentication_token',
                    ],
                    'camps' => [
                        'href' => '/camps',
                    ],
                ],
            ]
        ));
    }

    /**
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testCreateTemplatedLinkIfParameters() {
        $this->paths->addPath('/camps/{id}', new PathItem(get: $this->PATH_PARAMETER));
        $resource = new Entrypoint(new ResourceNameCollection([Camp::class]));
        $this->resourceMetadataFactory->expects($this->once())
            ->method('create')
            ->willReturn(new ResourceMetadata('camp', collectionOperations: [$this->PATH_PARAMETER]))
        ;

        $normalize = $this->uriTemplateNormalizer->normalize($resource);

        self::assertThat($normalize, self::equalTo(
            [
                '_links' => [
                    'self' => [
                        'href' => '/',
                    ],
                    'auth' => [
                        'href' => '/authentication_token',
                    ],
                    'camps' => [
                        'href' => '/camps{/id}',
                        'templated' => true,
                    ],
                ],
            ]
        ));
    }

    /**
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testCreateTemplatedLinksForQueryParameters() {
        $this->paths->addPath('/activities', new PathItem(get: $this->QUERY_PARAMETER));
        $resource = new Entrypoint(new ResourceNameCollection([Activity::class]));
        $this->resourceMetadataFactory->expects($this->once())
            ->method('create')
            ->willReturn(new ResourceMetadata('activity', collectionOperations: [$this->QUERY_PARAMETER]))
        ;

        $normalize = $this->uriTemplateNormalizer->normalize($resource);

        self::assertThat($normalize, self::equalTo(
            [
                '_links' => [
                    'self' => [
                        'href' => '/',
                    ],
                    'auth' => [
                        'href' => '/authentication_token',
                    ],
                    'activities' => [
                        'href' => '/activities{?camp,camp[]}',
                        'templated' => true,
                    ],
                ],
            ]
        ));
    }

    /**
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testMergePathAndQueryParameter() {
        $this->paths->addPath('/activities/{id}', new PathItem(get: $this->PATH_AND_QUERY_PARAMETER));
        $resource = new Entrypoint(new ResourceNameCollection([Activity::class]));
        $this->resourceMetadataFactory->expects($this->once())
            ->method('create')
            ->willReturn(new ResourceMetadata('activity', collectionOperations: [$this->PATH_AND_QUERY_PARAMETER]))
        ;

        $normalize = $this->uriTemplateNormalizer->normalize($resource);

        self::assertThat($normalize, self::equalTo(
            [
                '_links' => [
                    'self' => [
                        'href' => '/',
                    ],
                    'auth' => [
                        'href' => '/authentication_token',
                    ],
                    'activities' => [
                        'href' => '/activities{/id}{?camp,camp[]}',
                        'templated' => true,
                    ],
                ],
            ]
        ));
    }
}
