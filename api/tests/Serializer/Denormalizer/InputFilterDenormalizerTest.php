<?php

namespace App\Tests\Serializer\Denormalizer;

use App\InputFilter\FilterAttribute;
use App\InputFilter\InputFilter;
use App\Serializer\Denormalizer\InputFilterDenormalizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Validator\Constraints\Valid;

class InputFilterDenormalizerTest extends TestCase {
    private InputFilterDenormalizer $denormalizer;

    /**
     * @var ContextAwareDenormalizerInterface|MockObject
     */
    private $decoratedMock;

    /**
     * @var ServiceLocator|MockObject
     */
    private $filterLocatorMock;

    protected function setUp(): void {
        $this->filterLocatorMock = $this->createMock(ServiceLocator::class);
        $this->filterLocatorMock->method('get')->willReturnCallback(function ($name) {
            return new $name();
        });

        $this->decoratedMock = $this->createMock(ContextAwareDenormalizerInterface::class);
        $this->denormalizer = new InputFilterDenormalizer($this->filterLocatorMock);
        $this->denormalizer->setDenormalizer($this->decoratedMock);
    }

    public function testDenormalize() {
        $this->decoratedMock->expects($this->once())
            ->method('denormalize')
            ->willReturnArgument(0);

        $result = $this->denormalizer->denormalize(['foo' => 'test'], DummyEntity::class);

        $this->assertEquals(['foo' => 'processed'], $result);
    }

    public function testPrioritizesInputFilters() {
        $this->decoratedMock->expects($this->once())
            ->method('denormalize')
            ->willReturnArgument(0);

        $result = $this->denormalizer->denormalize(['ab' => 'xxx', 'ba' => 'yyy'], DummyEntity::class);

        $this->assertEquals(['ab' => 'xxxAB', 'ba' => 'yyyBA'], $result);
    }

    public function testSupportsValidArray() {
        $this->assertTrue(
            $this->denormalizer->supportsDenormalization(
                ['a' => 123],
                DummyEntity::class,
                'json',
                ['con' => 'text']
            )
        );
    }

    public function testDoesntSupportNonArray() {
        $this->assertFalse(
            $this->denormalizer->supportsDenormalization(
                new \stdClass(),
                DummyEntity::class,
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

        $this->denormalizer->denormalize([], DummyEntity::class, 'json', $context);

        $this->assertFalse($this->denormalizer->supportsDenormalization([], DummyEntity::class, 'json', $context));
    }
}

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Dummy extends FilterAttribute {}

class DummyFilter extends InputFilter {
    function applyTo(array $data, string $propertyName): array {
        if (!isset($data[$propertyName])) return $data;
        return [$propertyName => 'processed'];
    }
}

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class AppendA extends FilterAttribute {}

class AppendAFilter extends InputFilter {
    function applyTo(array $data, string $propertyName): array {
        if (!isset($data[$propertyName])) return $data;
        $data[$propertyName] = $data[$propertyName].'A';
        return $data;
    }
}

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class AppendB extends FilterAttribute {}

class AppendBFilter extends InputFilter {
    function applyTo(array $data, string $propertyName): array {
        if (!isset($data[$propertyName])) return $data;
        $data[$propertyName] = $data[$propertyName].'B';
        return $data;
    }
}

class DummyEntity {
    #[Dummy]
    public $foo;

    // other attribute which is not an input filter
    #[Valid]
    public $bar;

    #[AppendA(priority: 10)]
    #[AppendB(priority: 0)]
    public $ab;

    #[AppendA(priority: 0)]
    #[AppendB(priority: 10)]
    public $ba;

    public function __construct($foo, $bar) {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
