<?php

namespace App\Tests\Serializer\Denormalizer;

use App\Entity\BaseEntity;
use App\InputFilter\FilterAttribute;
use App\InputFilter\InputFilter;
use App\Serializer\Denormalizer\InputFilterDenormalizer;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @internal
 */
class InputFilterDenormalizerTest extends TestCase {
    private InputFilterDenormalizer $denormalizer;

    /**
     * @var ContextAwareDenormalizerInterface|MockObject
     */
    private $decoratedMock;

    /**
     * @var MockObject|ServiceLocator
     */
    private $inputFilterLocatorMock;

    protected function setUp(): void {
        $this->inputFilterLocatorMock = $this->createMock(ServiceLocator::class);
        $this->inputFilterLocatorMock->method('get')->willReturnCallback(function ($name) {
            return new $name();
        });

        $this->decoratedMock = $this->createMock(ContextAwareDenormalizerInterface::class);
        $this->denormalizer = new InputFilterDenormalizer($this->inputFilterLocatorMock);
        $this->denormalizer->setDenormalizer($this->decoratedMock);
    }

    public function testDenormalize() {
        $this->decoratedMock->expects($this->once())
            ->method('denormalize')
            ->willReturnArgument(0)
        ;

        $result = $this->denormalizer->denormalize(['foo' => 'test'], DummyEntity::class);

        $this->assertEquals(['foo' => 'processed'], $result);
    }

    public function testPrioritizesInputFilters() {
        $this->decoratedMock->expects($this->once())
            ->method('denormalize')
            ->willReturnArgument(0)
        ;

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
            })
        ;

        $this->denormalizer->denormalize([], DummyEntity::class, 'json', $context);

        $this->assertFalse($this->denormalizer->supportsDenormalization([], DummyEntity::class, 'json', $context));
    }

    public function testDoesNotApplyDenormalizerIfRelatedEntityIsNotPresent() {
        $this->decoratedMock->expects($this->once())
            ->method('denormalize')
            ->willReturnArgument(0)
        ;

        $result = $this->denormalizer->denormalize(['foo' => 'test'], DummyEntity::class);

        $this->assertEquals(['foo' => 'processed'], $result);
    }

    public function testDoesNotApplyDenormalizerIfRelatedEntityIsIRI() {
        $this->decoratedMock->expects($this->once())
            ->method('denormalize')
            ->willReturnArgument(0)
        ;

        $result = $this->denormalizer->denormalize(
            [
                'foo' => 'test',
                'relatedEntity' => '/relatedEntities/1',
            ],
            DummyEntity::class
        );

        $this->assertEquals(
            [
                'foo' => 'processed',
                'relatedEntity' => '/relatedEntities/1',
            ],
            $result
        );
    }

    public function testDenormalizeRelatedEntity() {
        $this->decoratedMock->expects($this->once())
            ->method('denormalize')
            ->willReturnArgument(0)
        ;

        $result = $this->denormalizer->denormalize(
            [
                'foo' => 'test',
                'relatedEntity' => [
                    'dummy' => 'test',
                    'dummy2' => 'test',
                    'a' => 'test',
                ],
            ],
            DummyEntity::class
        );

        $this->assertEquals(
            [
                'foo' => 'processed',
                'relatedEntity' => [
                    'dummy' => 'processed',
                    'dummy2' => 'processed',
                    'a' => 'testA',
                ],
            ],
            $result
        );
    }

    public function testDoesNotYetSupportEmbeddableStructuredProperties() {
        $this->decoratedMock->expects($this->once())
            ->method('denormalize')
            ->willReturnArgument(0)
        ;

        $result = $this->denormalizer->denormalize(
            [
                'foo' => 'test',
                'embeddableEntity' => [
                    'dummy' => 'test',
                ],
            ],
            DummyEntity::class
        );

        $this->assertEquals(
            [
                'foo' => 'processed',
                'embeddableEntity' => [
                    'dummy' => 'test',
                ],
            ],
            $result
        );
    }

    public function testDoesNotYetSupportCollectionProperties() {
        $this->decoratedMock->expects($this->once())
            ->method('denormalize')
            ->willReturnArgument(0)
        ;

        $result = $this->denormalizer->denormalize(
            [
                'foo' => 'test',
                'collection' => [
                    [
                        'dummy' => 'test',
                    ],
                ],
            ],
            DummyEntity::class
        );

        $this->assertEquals(
            [
                'foo' => 'processed',
                'collection' => [
                    [
                        'dummy' => 'test',
                    ],
                ],
            ],
            $result
        );
    }

    public function testDoesApplyFiltersToEmbeddedIRICollection() {
        $this->decoratedMock->expects($this->once())
            ->method('denormalize')
            ->willReturnArgument(0)
        ;

        $result = $this->denormalizer->denormalize(
            [
                'foo' => 'test',
                'collection' => [
                    'relatedEntities/1',
                    'relatedEntities/2',
                ],
            ],
            DummyEntity::class
        );

        $this->assertEquals(
            [
                'foo' => 'processed',
                'collection' => [
                    'relatedEntities/1',
                    'relatedEntities/2',
                ],
            ],
            $result
        );
    }
}

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Dummy extends FilterAttribute {
}

class DummyFilter extends InputFilter {
    public function applyTo(array $data, string $propertyName): array {
        if (!isset($data[$propertyName])) {
            return $data;
        }
        $data[$propertyName] = 'processed';

        return $data;
    }
}

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class AppendA extends FilterAttribute {
}

class AppendAFilter extends InputFilter {
    public function applyTo(array $data, string $propertyName): array {
        if (!isset($data[$propertyName])) {
            return $data;
        }
        $data[$propertyName] = $data[$propertyName].'A';

        return $data;
    }
}

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class AppendB extends FilterAttribute {
}

class AppendBFilter extends InputFilter {
    public function applyTo(array $data, string $propertyName): array {
        if (!isset($data[$propertyName])) {
            return $data;
        }
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

    /**
     * @ORM\OneToOne(targetEntity="RelatedEntity")
     */
    public RelatedEntity $relatedEntity;

    /**
     * @ORM\OneToMany(targetEntity="RelatedEntity")
     */
    public Collection $collection;

    /**
     * @ORM\Embedded(class="EmbeddableEntity")
     */
    public EmbeddableEntity $embeddableEntity;

    public function __construct($foo, $bar) {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}

class RelatedEntity extends BaseEntity {
    #[Dummy]
    public string $dummy;

    #[Dummy]
    public string $dummy2;

    #[AppendA]
    public string $a;
}

/** @ORM\Embeddable */
class EmbeddableEntity {
    #[Dummy]
    public string $dummy;
}
