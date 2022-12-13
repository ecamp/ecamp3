<?php

namespace App\Tests\Serializer\Normalizer;

use ApiPlatform\Api\Entrypoint;
use ApiPlatform\Api\UrlGeneratorInterface;
use ApiPlatform\Metadata\Resource\ResourceNameCollection;
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
    private array $loginAndOauthLinks;

    protected function setUp(): void {
        $this->decorated = $this->createMock(NormalizerInterface::class);
        $this->uriTemplateFactory = $this->createMock(UriTemplateFactory::class);
        $this->englishInflector = new EnglishInflector();
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturnCallback(function (string $arg) {
            switch ($arg) {
                case 'authentication_token':
                    return '/authentication_token';

                case 'connect_google_start':
                    return '/auth/google';

                case 'connect_pbsmidata_start':
                    return '/auth/pbsmidata';

                case 'connect_cevidb_start':
                    return '/auth/cevidb';

                case '_api_/auth/reset_password{._format}_post':
                    return '/auth/reset_password';

                default:
                    return null;
            }
        });

        $this->uriTemplateNormalizer = new UriTemplateNormalizer(
            $this->decorated,
            $this->englishInflector,
            $this->uriTemplateFactory,
            $urlGenerator,
        );

        $this->loginAndOauthLinks = [
            'login' => [
                'href' => '/authentication_token',
            ],
            'oauthGoogle' => [
                'href' => '/auth/google{?callback}',
                'templated' => true,
            ],
            'oauthPbsmidata' => [
                'href' => '/auth/pbsmidata{?callback}',
                'templated' => true,
            ],
            'oauthCevidb' => [
                'href' => '/auth/cevidb{?callback}',
                'templated' => true,
            ],
            'resetPassword' => [
                'href' => '/auth/reset_password{/id}',
                'templated' => true,
            ],
        ];
    }

    public function testCreateNotTemplatedLinkIfNoParameters() {
        // given
        $this->decorated->expects($this->once())->method('normalize')->willReturn(['_links' => [
            'self' => ['href' => '/'],
            'camp' => ['href' => '/camps'],
        ]]);
        $resource = new Entrypoint(new ResourceNameCollection([Camp::class]));
        $this->uriTemplateFactory->expects($this->once())->method('createFromShortname')->willReturn(['/camps', false]);

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
                    ...$this->loginAndOauthLinks,
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
        $this->uriTemplateFactory->expects($this->once())->method('createFromShortname')->willReturn(['/camps{/id}', true]);

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
                    ...$this->loginAndOauthLinks,
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
        $this->uriTemplateFactory->expects($this->once())->method('createFromShortname')->willReturn(['/activities{?camp,camp[]}', true]);

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
                    ...$this->loginAndOauthLinks,
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
        $this->uriTemplateFactory->expects($this->once())->method('createFromShortname')->willReturn(['/activities{/id}{?camp,camp[]}', true]);

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
                    ...$this->loginAndOauthLinks,
                ],
            ]
        ));
    }
}
