<?php

namespace App\Tests\State\Util;

use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\BaseEntity;
use App\State\Util\AbstractPersistProcessor;
use App\State\Util\PropertyChangeListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class AbstractPersistProcessorTest extends TestCase {
    private MockObject|ProcessorInterface $decoratedProcessor;
    private MockObject|MockableClosure $closure;
    private PropertyChangeListener $propertyChangeListener;
    private MockObject|AbstractPersistProcessor $processor;

    protected function setUp(): void {
        $this->decoratedProcessor = $this->createMock(ProcessorInterface::class);
        $this->decoratedProcessor->method('process')->willReturnArgument(0);

        $this->closure = $this->createMock(MockableClosure::class);

        $this->propertyChangeListener = PropertyChangeListener::of(
            extractProperty: fn ($data) => $data->name,
            beforeAction: fn ($data) => $this->closure->call($data),
            afterAction: fn ($data) => $this->closure->call($data),
        );

        $this->processor = $this->getMockForAbstractClass(
            AbstractPersistProcessor::class,
            [
                $this->decoratedProcessor,
                [$this->propertyChangeListener],
            ],
            mockedMethods: ['onBefore', 'onAfter']
        );

        $this->processor->method('onBefore')->willReturnArgument(0);
    }

    public function testThrowsIfOnePropertyChangeListenerIsOfWrongType() {
        $this->expectException(\InvalidArgumentException::class);
        $this->getMockForAbstractClass(
            AbstractPersistProcessor::class,
            [
                $this->decoratedProcessor,
                [$this->propertyChangeListener, new \stdClass()],
            ]
        );
    }

    public function testCallsOnBeforeCreateAndOnAfterCreateOnPost() {
        $toPersist = new MyEntity();

        $this->processor->expects(self::once())->method('onBefore')->willReturnArgument(0);
        $this->processor->expects(self::once())->method('onAfter');
        $this->decoratedProcessor->expects(self::once())->method('process')->willReturnArgument(0);

        $processResult = $this->processor->process($toPersist, new Post());
        self::assertThat($processResult, self::equalTo($toPersist));
    }

    /**
     * @throws \ReflectionException
     */
    public function testNotCallPropertyChangeListenerIfDataWasNullBefore() {
        $toPersist = new MyEntity();

        $context = ['previous_data' => null];
        $this->propertyChangeListener = PropertyChangeListener::of(
            extractProperty: fn ($data) => $data->name,
            beforeAction: fn ($data) => $this->closure->call($data),
            afterAction: fn ($data) => $this->closure->call($data),
        );
        $this->closure->expects(self::never())->method('call');

        $this->processor->process($toPersist, new Patch(), [], $context);
    }

    /**
     * @throws \ReflectionException
     */
    public function testThrowErrorIfExtractPropertyFails() {
        $toPersist = new MyEmptyEntity();

        $context = ['previous_data' => $toPersist];
        $this->propertyChangeListener = PropertyChangeListener::of(
            extractProperty: fn ($data) => $data->name,
            afterAction: fn ($data) => $this->closure->call($data)
        );
        $this->closure->expects(self::never())->method('call');

        $this->expectWarning();
        $this->processor->process($toPersist, new Patch(), [], $context);
    }

    /**
     * @throws \ReflectionException
     */
    public function testNotCallPropertyChangeListenerIfPropertyDidNotChange() {
        $toPersist = new MyEntity();
        $toPersist->name = null;

        $context = ['previous_data' => $toPersist];
        $this->propertyChangeListener = PropertyChangeListener::of(
            extractProperty: fn ($data) => $data->name,
            beforeAction: fn ($data) => $this->closure->call($data),
            afterAction: fn ($data) => $this->closure->call($data),
        );
        $this->closure->expects(self::never())->method('call');

        $this->processor->process($toPersist, new Patch(), [], $context);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCallPropertyChangeListenerIfPropertyDidChange() {
        $oldData = new MyEntity();
        $oldData->name = null;
        $newData = new MyEntity();
        $newData->name = 'test';

        $context = ['previous_data' => $oldData];

        $this->propertyChangeListener = PropertyChangeListener::of(
            extractProperty: fn ($data) => $data->name,
            beforeAction: fn ($data) => $this->closure->call($data),
            afterAction: fn ($data) => $this->closure->call($data),
        );
        $this->decoratedProcessor->expects(self::once())
            ->method('process')
            ->willReturnArgument(0)
        ;
        $this->closure->expects(self::exactly(2))
            ->method('call')
            ->willReturnOnConsecutiveCalls($newData, null)
        ;
        $this->processor->expects(self::once())->method('onBefore')->willReturnArgument(0);
        $this->processor->expects(self::once())->method('onAfter');

        $processResult = $this->processor->process($newData, new Patch(), [], $context);
        self::assertThat($processResult, self::equalTo($newData));
    }
}

class MyEntity extends BaseEntity {
    public ?string $name = 'test';
}

class MyEmptyEntity extends BaseEntity {
}
