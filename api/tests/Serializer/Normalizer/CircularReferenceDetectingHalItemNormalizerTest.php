<?php

namespace App\Tests\Serializer\Normalizer;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Api\ResourceClassResolverInterface;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use App\Serializer\Normalizer\CircularReferenceDetectingHalItemNormalizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
class CircularReferenceDetectingHalItemNormalizerTest extends TestCase {
    private CircularReferenceDetectingHalItemNormalizer $normalizer;

    /**
     * @var MockObject|NormalizerInterface
     */
    private $decoratedMock;

    /**
     * @var IriConverterInterface|MockObject
     */
    private $iriConverterMock;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(NormalizerInterface::class);
        $this->iriConverterMock = $this->createMock(IriConverterInterface::class);

        $this->normalizer = new CircularReferenceDetectingHalItemNormalizer(
            $this->decoratedMock,
            $this->createMock(PropertyNameCollectionFactoryInterface::class),
            $this->createMock(PropertyMetadataFactoryInterface::class),
            $this->iriConverterMock,
            $this->createMock(ResourceClassResolverInterface::class)
        );

        $this->normalizer->setSerializer($this->createMock(SerializerInterface::class));
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

    public function testDelegatesNormalizeToDecorated() {
        // given
        $resource = new Dummy();
        $delegatedResult = [
            'hello' => 'world',
            '_links' => [
                'items' => [['href' => '/children/1']],
            ],
        ];
        $this->decoratedMock->expects($this->once())
            ->method('normalize')
            ->willReturn($delegatedResult)
        ;

        // when
        $result = $this->normalizer->normalize($resource, null, []);

        // then
        $this->assertEquals($delegatedResult, $result);
    }

    public function testNormalizeDetectsCircularReference() {
        // given
        $normalizer = $this->normalizer;
        $resource = new Dummy();
        $related = new RelatedDummy();
        $resource->related = $related;
        $related->owner = $resource;

        $this->decoratedMock
            ->method('normalize')
            ->willReturnCallback(function ($object, $format, $context) use ($normalizer) {
                $result = ['serialized_id' => $object->id];
                if ($object instanceof Dummy) {
                    $result = array_merge($result, ['related' => $normalizer->normalize($object->related, $format, $context)]);
                }
                if ($object instanceof RelatedDummy) {
                    $result = array_merge($result, ['owner' => $normalizer->normalize($object->owner, $format, $context)]);
                }

                return $result;
            })
        ;
        $this->iriConverterMock
            ->method('getIriFromResource')
            ->willReturnCallback(fn ($object) => '/'.$object->id)
        ;

        // when
        /** @var Dummy $result */
        $result = $this->normalizer->normalize($resource, null, []);

        // then
        $this->assertEquals(['_links' => ['self' => ['href' => '/dummy']]], $result['related']['owner']);
    }
}

class Dummy {
    public string $id = 'dummy';
    public ?RelatedDummy $related = null;
}

class RelatedDummy {
    public string $id = 'related';
    public ?Dummy $owner = null;
}
