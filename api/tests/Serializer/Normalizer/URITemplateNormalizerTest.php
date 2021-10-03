<?php

namespace App\Tests\Serializer\Normalizer;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Info;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Paths;
use ApiPlatform\Core\OpenApi\OpenApi;
use App\Serializer\Normalizer\URITemplateNormalizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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

    protected function setUp(): void {
        $idInPathParameter = new Parameter(name: 'id', in: 'path');
        $campQueryParameter = new Parameter(name: 'camp', in: 'query');
        $campArrayQueryParameter = new Parameter(name: 'camp[]', in: 'query');

        $this->OPERATION_WITHOUT_PARAMETER = new Operation();
        $this->PATH_PARAMETER = new Operation(parameters: [$idInPathParameter]);
        $this->QUERY_PARAMETER = new Operation(parameters: [$campQueryParameter, $campArrayQueryParameter]);
        $this->PATH_AND_QUERY_PARAMETER = new Operation(parameters: [$idInPathParameter, $campQueryParameter, $campArrayQueryParameter]);

        $this->decorated = $this->createMock(NormalizerInterface::class);
        $openApiFactoryInterface = $this->createMock(OpenApiFactoryInterface::class);
        $this->uriTemplateNormalizer = new URITemplateNormalizer($this->decorated, $openApiFactoryInterface);

        $this->paths = new Paths();

        $openApi = new OpenApi(info: new Info('', ''), servers: [], paths: $this->paths);
        $openApiFactoryInterface->method('__invoke')->willReturn($openApi);
    }

    /**
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testCreateNotTemplatedLinkIfNoParameters() {
        $this->decorated->expects(self::once())->method('normalize')->willReturn(['_links' => ['self' => '']]);
        $this->paths->addPath('/camps', new PathItem(get: $this->OPERATION_WITHOUT_PARAMETER));

        $normalize = $this->uriTemplateNormalizer->normalize(null);

        self::assertThat($normalize, self::equalTo(
            [
                '_links' => [
                    'self' => '',
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
        $this->decorated->expects(self::once())->method('normalize')->willReturn(['_links' => ['self' => '']]);
        $this->paths->addPath('/camps/{id}', new PathItem(get: $this->PATH_PARAMETER));

        $normalize = $this->uriTemplateNormalizer->normalize(null);

        self::assertThat($normalize, self::equalTo(
            [
                '_links' => [
                    'self' => '',
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
        $this->decorated->expects(self::once())->method('normalize')->willReturn(['_links' => ['self' => '']]);
        $this->paths->addPath('/activities', new PathItem(get: $this->QUERY_PARAMETER));

        $normalize = $this->uriTemplateNormalizer->normalize(null);

        self::assertThat($normalize, self::equalTo(
            [
                '_links' => [
                    'self' => '',
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
        $this->decorated->expects(self::once())->method('normalize')->willReturn(['_links' => ['self' => '']]);
        $this->paths->addPath('/activities/{id}', new PathItem(get: $this->PATH_AND_QUERY_PARAMETER));

        $normalize = $this->uriTemplateNormalizer->normalize(null);

        self::assertThat($normalize, self::equalTo(
            [
                '_links' => [
                    'self' => '',
                    'activities' => [
                        'href' => '/activities{/id}{?camp,camp[]}',
                        'templated' => true,
                    ],
                ],
            ]
        ));
    }
}
