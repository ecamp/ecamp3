<?php

namespace App\Tests\Serializer\Normalizer;

use ApiPlatform\Core\Api\Entrypoint;
use ApiPlatform\Core\Metadata\Resource\ResourceNameCollection;
use App\Entity\Activity;
use App\Entity\Camp;
use App\Metadata\Resource\Factory\UriTemplateFactory;
use App\Serializer\Normalizer\UriTemplateNormalizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

/**
 * @internal
 */
class UriTemplateNormalizerTest extends TestCase {
    private UriTemplateNormalizer $uriTemplateNormalizer;
    private MockObject|NormalizerInterface $decorated;
    private MockObject|UriTemplateFactory $uriTemplateFactory;
    private EnglishInflector $englishInflector;

    protected function setUp(): void {
        $this->decorated = $this->createMock(NormalizerInterface::class);
        $this->uriTemplateFactory = $this->createMock(UriTemplateFactory::class);
        $this->englishInflector = new EnglishInflector();

        $this->uriTemplateNormalizer = new UriTemplateNormalizer(
            $this->decorated,
            $this->englishInflector,
            $this->uriTemplateFactory,
        );
    }

    public function testCreateNotTemplatedLinkIfNoParameters() {
        // given
        $this->decorated->expects($this->once())->method('normalize')->willReturn(['_links' => [
            'self' => ['href' => '/'],
            'camp' => ['href' => '/camps'],
        ]]);
        $resource = new Entrypoint(new ResourceNameCollection([Camp::class]));
        $this->uriTemplateFactory->expects($this->once())->method('create')->willReturn(['/camps', false]);

        // when
        $normalize = $this->uriTemplateNormalizer->normalize($resource);

        // then
        self::assertThat($normalize, self::equalTo(
            [
                '_links' => [
                    'self' => [
                        'href' => '/',
                    ],
                    'camps' => [
                        'href' => '/camps',
                    ],
                ],
            ]
        ));
    }

    public function testCreateTemplatedLinkIfPathParameters() {
        // given
        $this->decorated->expects($this->once())->method('normalize')->willReturn(['_links' => [
            'self' => ['href' => '/'],
            'camp' => ['href' => '/camps'],
        ]]);
        $resource = new Entrypoint(new ResourceNameCollection([Camp::class]));
        $this->uriTemplateFactory->expects($this->once())->method('create')->willReturn(['/camps{/id}', true]);

        // when
        $normalize = $this->uriTemplateNormalizer->normalize($resource);

        // then
        self::assertThat($normalize, self::equalTo(
            [
                '_links' => [
                    'self' => [
                        'href' => '/',
                    ],
                    'camps' => [
                        'href' => '/camps{/id}',
                        'templated' => true,
                    ],
                ],
            ]
        ));
    }

    public function testCreateTemplatedLinkForQueryParameters() {
        // given
        $this->decorated->expects($this->once())->method('normalize')->willReturn(['_links' => [
            'self' => ['href' => '/'],
            'activity' => ['href' => '/activities'],
        ]]);
        $resource = new Entrypoint(new ResourceNameCollection([Activity::class]));
        $this->uriTemplateFactory->expects($this->once())->method('create')->willReturn(['/activities{?camp,camp[]}', true]);

        // when
        $normalize = $this->uriTemplateNormalizer->normalize($resource);

        // then
        self::assertThat($normalize, self::equalTo(
            [
                '_links' => [
                    'self' => [
                        'href' => '/',
                    ],
                    'activities' => [
                        'href' => '/activities{?camp,camp[]}',
                        'templated' => true,
                    ],
                ],
            ]
        ));
    }

    public function testMergePathAndQueryParameter() {
        // given
        $this->decorated->expects($this->once())->method('normalize')->willReturn(['_links' => [
            'self' => ['href' => '/'],
            'activity' => ['href' => '/activities'],
        ]]);
        $resource = new Entrypoint(new ResourceNameCollection([Activity::class]));
        $this->uriTemplateFactory->expects($this->once())->method('create')->willReturn(['/activities{/id}{?camp,camp[]}', true]);

        // when
        $normalize = $this->uriTemplateNormalizer->normalize($resource);

        // then
        self::assertThat($normalize, self::equalTo(
            [
                '_links' => [
                    'self' => [
                        'href' => '/',
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
