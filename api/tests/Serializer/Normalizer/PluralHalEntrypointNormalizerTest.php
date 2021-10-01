<?php

namespace App\Tests\Serializer\Normalizer;

use ApiPlatform\Core\Api\Entrypoint;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\Metadata\Resource\ResourceNameCollection;
use App\Entity\Camp;
use App\Serializer\Normalizer\PluralHalEntrypointNormalizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

/**
 * @internal
 */
class PluralHalEntrypointNormalizerTest extends TestCase {
    private PluralHalEntrypointNormalizer $normalizer;

    /**
     * @var MockObject|NormalizerInterfaceWithCacheableSupportsMethodInterface
     */
    private $decoratedMock;
    /**
     * @var EnglishInflector
     */
    private $inflector;

    /**
     * @var MockObject|ResourceMetadataFactoryInterface
     */
    private $resourceMetadataFactory;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(NormalizerInterfaceWithCacheableSupportsMethodInterface::class);
        $this->inflector = new EnglishInflector();
        $this->resourceMetadataFactory = $this->createMock(ResourceMetadataFactoryInterface::class);

        $this->normalizer = new PluralHalEntrypointNormalizer(
            $this->decoratedMock,
            $this->inflector,
            $this->resourceMetadataFactory
        );
    }

    public function testDelegatesSupportCheckToDecorated() {
        $this->decoratedMock
            ->expects($this->exactly(2))
            ->method('supportsNormalization')
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        $this->assertTrue($this->normalizer->supportsNormalization([]));
        $this->assertFalse($this->normalizer->supportsNormalization([]));
    }

    public function testDelegatesHasCacheableSupportsMethodToDecorated() {
        $this->decoratedMock
            ->expects($this->exactly(2))
            ->method('hasCacheableSupportsMethod')
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        $this->assertTrue($this->normalizer->hasCacheableSupportsMethod());
        $this->assertFalse($this->normalizer->hasCacheableSupportsMethod());
    }

    public function testDelegatesNormalizeToDecorated() {
        // given
        $resource = new Entrypoint(new ResourceNameCollection([]));
        $delegatedResult = [
            '_links' => [
                'self' => ['href' => '/'],
            ],
        ];
        $this->decoratedMock->expects($this->once())
            ->method('normalize')
            ->willReturn($delegatedResult)
        ;

        // when
        $result = $this->normalizer->normalize($resource);

        // then
        $this->assertEquals($delegatedResult, $result);
    }

    public function testConvertsRelationNameToPlural() {
        // given
        $resource = new Entrypoint(new ResourceNameCollection([Camp::class]));
        $delegatedResult = [
            '_links' => [
                'self' => ['href' => '/'],
                'camp' => ['href' => '/camps'],
            ],
        ];
        $this->decoratedMock->expects($this->once())
            ->method('normalize')
            ->willReturn($delegatedResult)
        ;
        $this->resourceMetadataFactory->expects($this->once())
            ->method('create')
            ->willReturn(new ResourceMetadata('camp'))
        ;

        // when
        $result = $this->normalizer->normalize($resource);

        // then
        $this->assertEquals([
            '_links' => [
                'self' => ['href' => '/'],
                'camps' => ['href' => '/camps'],
            ],
        ], $result);
    }

    public function testIgnoresNonexistentRelation() {
        // given
        $resource = new Entrypoint(new ResourceNameCollection([Camp::class]));
        $delegatedResult = [
            '_links' => [
                'self' => ['href' => '/'],
                // not in output from decorated normalizer
                // 'camp' => ['href' => '/camps'],
            ],
        ];
        $this->decoratedMock->expects($this->once())
            ->method('normalize')
            ->willReturn($delegatedResult)
        ;
        $this->resourceMetadataFactory->expects($this->once())
            ->method('create')
            ->willReturn(new ResourceMetadata('camp'))
        ;

        // when
        $result = $this->normalizer->normalize($resource);

        // then
        $this->assertEquals([
            '_links' => [
                'self' => ['href' => '/'],
            ],
        ], $result);
    }
}

interface NormalizerInterfaceWithCacheableSupportsMethodInterface extends NormalizerInterface, CacheableSupportsMethodInterface {
}
