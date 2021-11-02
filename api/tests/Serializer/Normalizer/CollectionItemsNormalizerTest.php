<?php

namespace App\Tests\Serializer\Normalizer;

use App\Serializer\Normalizer\CollectionItemsNormalizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @internal
 */
class CollectionItemsNormalizerTest extends TestCase {
    private CollectionItemsNormalizer $normalizer;

    /**
     * @var MockObject|NormalizerInterface
     */
    private $decoratedMock;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(ContextAwareNormalizerInterface::class);

        $this->normalizer = new CollectionItemsNormalizer($this->decoratedMock);
        $this->normalizer->setNormalizer($this->createMock(NormalizerInterface::class));
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
        $resource = [];
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

    public function testNormalizeReplacesEmbeddedAndLinkedItemWithItems() {
        // given
        $resource = [];
        $this->mockDecoratedNormalizer();

        // when
        $result = $this->normalizer->normalize($resource, null, []);

        // then
        $this->assertEquals([
            'hello' => 'world',
            '_links' => [
                'items' => [
                    ['href' => '/children/1'],
                    ['href' => '/children/2'],
                ],
            ],
            '_embedded' => [
                'items' => [
                    ['name' => 'One', '_links' => ['self' => ['href' => '/children/1']]],
                    ['name' => 'Two', '_links' => ['self' => ['href' => '/children/2']]],
                ],
            ],
        ], $result);
    }

    public function testNormalizeAddsEmptyEmbeddedItemsIfTotalItemsIsZero() {
        // given
        $resource = [];
        $this->decoratedMock->method('normalize')->willReturn([
            'hello' => 'world',
            'totalItems' => 0,
            '_links' => [
            ],
            '_embedded' => [
            ],
        ]);

        // when
        $result = $this->normalizer->normalize($resource, null, []);

        // then
        $this->assertEquals([
            'hello' => 'world',
            'totalItems' => 0,
            '_links' => [
            ],
            '_embedded' => [
                'items' => [],
            ],
        ], $result);
    }

    protected function mockDecoratedNormalizer() {
        $this->decoratedMock->method('normalize')->willReturn([
            'hello' => 'world',
            '_links' => [
                'item' => [
                    ['href' => '/children/1'],
                    ['href' => '/children/2'],
                ],
            ],
            '_embedded' => [
                'item' => [
                    ['name' => 'One', '_links' => ['self' => ['href' => '/children/1']]],
                    ['name' => 'Two', '_links' => ['self' => ['href' => '/children/2']]],
                ],
            ],
        ]);
    }
}
