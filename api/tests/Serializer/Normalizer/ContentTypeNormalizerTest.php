<?php

namespace App\Tests\Serializer\Normalizer;

use ApiPlatform\Core\Bridge\Symfony\Routing\RouteNameResolverInterface;
use App\Entity\ContentType;
use App\Serializer\Normalizer\ContentTypeNormalizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
class ContentTypeNormalizerTest extends TestCase {
    private ContentTypeNormalizer $normalizer;

    private MockObject|NormalizerInterface $decoratedMock;
    private MockObject|RouteNameResolverInterface $routeNameResolver;
    private MockObject|RouterInterface $routerMock;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(ContextAwareNormalizerInterface::class);
        $this->routeNameResolver = $this->createMock(RouteNameResolverInterface::class);
        $this->routerMock = $this->createMock(RouterInterface::class);

        $this->normalizer = new ContentTypeNormalizer(
            $this->decoratedMock,
            $this->routeNameResolver,
            $this->routerMock,
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
        $resource = new \stdClass();
        $delegatedResult = [
            'hello' => 'world',
        ];
        $this->decoratedMock->expects($this->once())
            ->method('normalize')
            ->willReturn($delegatedResult)
        ;

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => DummyEntity::class]);

        // then
        $this->assertEquals($delegatedResult, $result);
    }

    public function testNormalizeAddsEntityPath() {
        // given
        $resource = new ContentType();
        $delegatedResult = [
            'hello' => 'world',
            'entityClass' => 'DummyClass',
        ];
        $this->decoratedMock->expects($this->once())
            ->method('normalize')
            ->willReturn($delegatedResult)
         ;
        $this->routerMock->expects($this->once())
            ->method('generate')
            ->willReturn('/path')
        ;
        $this->routeNameResolver->expects($this->once())
            ->method('getRouteName')
        ;

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => DummyEntity::class]);

        // then
        $expectedResult = [
            'hello' => 'world',
            'entityClass' => 'DummyClass',
            'entityPath' => '/path',
        ];
        $this->assertEquals($expectedResult, $result);
    }
}
