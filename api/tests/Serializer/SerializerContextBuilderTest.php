<?php

namespace App\Tests\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Serializer\SerializerContextBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
class SerializerContextBuilderTest extends TestCase {
    private SerializerContextBuilder $contextBuilder;

    /**
     * @var MockObject|SerializerContextBuilderInterface
     */
    private $decoratedMock;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(SerializerContextBuilderInterface::class);

        $this->contextBuilder = new SerializerContextBuilder($this->decoratedMock);
    }

    public function testAddsSkipNullValuesFalseWhenNormalizing() {
        $request = $this->createMock(Request::class);
        $this->decoratedMock
            ->expects($this->exactly(1))
            ->method('createFromRequest')
            ->willReturn([])
        ;

        $result = $this->contextBuilder->createFromRequest($request, true);

        $this->assertEquals(['skip_null_values' => false], $result);
    }

    public function testDoesntAddSkipNullValuesFalseWhenDenormalizing() {
        $request = $this->createMock(Request::class);
        $this->decoratedMock
            ->expects($this->exactly(1))
            ->method('createFromRequest')
            ->willReturn([])
        ;

        $result = $this->contextBuilder->createFromRequest($request, false);

        $this->assertNotEquals(['skip_null_values' => false], $result);
    }

    public function testDoesntAddAllowExtraAttributesFalseWhenNormalizing() {
        $request = $this->createMock(Request::class);
        $this->decoratedMock
            ->expects($this->exactly(1))
            ->method('createFromRequest')
            ->willReturn([])
        ;

        $result = $this->contextBuilder->createFromRequest($request, true);

        $this->assertNotEquals(['allow_extra_attributes' => false], $result);
    }

    public function testAddsAllowExtraAttributesFalseWhenDenormalizing() {
        $request = $this->createMock(Request::class);
        $this->decoratedMock
            ->expects($this->exactly(1))
            ->method('createFromRequest')
            ->willReturn([])
        ;

        $result = $this->contextBuilder->createFromRequest($request, false);

        $this->assertEquals(['allow_extra_attributes' => false], $result);
    }
}
