<?php

namespace App\Tests\Serializer\Denormalizer;

use App\InputFilter\InputFilter;
use App\Serializer\Denormalizer\InputFilterDenormalizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Validator\Constraints\Valid;

class InputFilterDenormalizerTest extends TestCase {
    private InputFilterDenormalizer $denormalizer;

    /**
     * @var ContextAwareDenormalizerInterface|MockObject
     */
    private $decoratedMock;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(ContextAwareDenormalizerInterface::class);
        $this->denormalizer = new InputFilterDenormalizer();
        $this->denormalizer->setDenormalizer($this->decoratedMock);
    }

    public function testDenormalize() {
        $this->decoratedMock->expects($this->once())
            ->method('denormalize')
            ->willReturnArgument(0);

        $result = $this->denormalizer->denormalize(['a' => 123, 'b' => 'test'], Dummy::class);

        $this->assertEquals(['foo' => 'processed'], $result);
    }

    public function testSupportsValidArray() {
        $this->assertTrue(
            $this->denormalizer->supportsDenormalization(
                ['a' => 123],
                Dummy::class,
                'json',
                ['con' => 'text']
            )
        );
    }

    public function testDoesntSupportNonArray() {
        $this->assertFalse(
            $this->denormalizer->supportsDenormalization(
                new \stdClass(),
                Dummy::class,
                'json',
                ['con' => 'text']
            )
        );
    }

    public function testDoesNotRecurseEndlessly() {
        $context = [];
        $this->decoratedMock->expects($this->once())
            ->method('denormalize')
            ->willReturnCallback(function ($data, $type, $format, $newContext) use (&$context) {
                $context = $newContext;
                return [];
            });

        $this->denormalizer->denormalize([], Dummy::class, 'json', $context);

        $this->assertFalse($this->denormalizer->supportsDenormalization([], Dummy::class, 'json', $context));
    }
}

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class DummyInputFilter extends InputFilter {
    function applyTo(array $data, string $propertyName): array {
        return [$propertyName => 'processed'];
    }
}

class Dummy {
    #[DummyInputFilter]
    public $foo;

    // other attribute which is not an input filter
    #[Valid]
    public $bar;

    public function __construct($foo, $bar) {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
