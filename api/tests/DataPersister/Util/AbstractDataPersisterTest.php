<?php

namespace App\Tests\DataPersister\Util;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataPersister\Util\AbstractDataPersister;
use App\DataPersister\Util\CustomActionListener;
use App\DataPersister\Util\DataPersisterObservable;
use App\DataPersister\Util\PropertyChangeListener;
use App\Entity\BaseEntity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @internal
 */
class AbstractDataPersisterTest extends TestCase {
    public const ACTION_NAME = 'myAction';

    private MockObject|ContextAwareDataPersisterInterface $contextAwareDataPersister;
    private DataPersisterObservable $dataPersisterObservable;
    private MockObject|MockableClosure $closure;
    private CustomActionListener $actionListener;
    private PropertyChangeListener $propertyChangeListener;
    private ParameterBag $parameterBag;
    private MockObject|AbstractDataPersister $dataPersister;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void {
        $this->contextAwareDataPersister = $this->createMock(ContextAwareDataPersisterInterface::class);
        $this->contextAwareDataPersister->method('persist')->willReturnArgument(0);

        $requestStack = $this->createMock(RequestStack::class);
        $request = $this->createMock(Request::class);
        $this->parameterBag = new ParameterBag(['previous_data' => null]);
        $request->attributes = &$this->parameterBag;
        $requestStack->method('getCurrentRequest')->willReturn($request);

        $this->dataPersisterObservable = new DataPersisterObservable($this->contextAwareDataPersister, $requestStack);

        $this->closure = $this->createMock(MockableClosure::class);

        $this->actionListener = CustomActionListener::of(
            self::ACTION_NAME,
            fn ($data) => $this->closure->call($data),
            fn ($data) => $this->closure->call($data)
        );

        $this->propertyChangeListener = PropertyChangeListener::of(
            extractProperty: fn ($data) => $data->name,
            beforeAction: fn ($data) => $this->closure->call($data),
            afterAction: fn ($data) => $this->closure->call($data),
        );

        $this->dataPersister = $this->getMockForAbstractClass(
            AbstractDataPersister::class,
            [
                MyEntity::class,
                $this->dataPersisterObservable,
                [$this->actionListener],
                [$this->propertyChangeListener],
            ],
            mockedMethods: ['beforeCreate', 'afterCreate', 'beforeUpdate', 'afterUpdate', 'beforeRemove', 'afterRemove']
        );
    }

    public function testThrowsIfOneActionListenerIsOfWrongType() {
        $this->expectException(\InvalidArgumentException::class);
        $this->getMockForAbstractClass(
            AbstractDataPersister::class,
            [
                MyEntity::class,
                $this->dataPersisterObservable,
                [$this->actionListener, new \stdClass()],
                [$this->propertyChangeListener],
            ]
        );
    }

    public function testThrowsIfOnePropertyChangeListenerIsOfWrongType() {
        $this->expectException(\InvalidArgumentException::class);
        $this->getMockForAbstractClass(
            AbstractDataPersister::class,
            [
                MyEntity::class,
                $this->dataPersisterObservable,
                [$this->actionListener],
                [$this->propertyChangeListener, new \stdClass()],
            ]
        );
    }

    public function testSupportsPassedEntityClassIfObservableSupportsIt() {
        $this->contextAwareDataPersister->expects(self::exactly(2))
            ->method('supports')
            ->willReturn(true, false)
        ;

        self::assertThat($this->dataPersister->supports(new MyEntity(), []), self::equalTo(true));
        self::assertThat($this->dataPersister->supports(new MyEntity(), []), self::equalTo(false));
    }

    public function testDoesNotSupportEntityClassIfObservableDoesNotSupportIt() {
        $this->contextAwareDataPersister->expects(self::any())
            ->method('supports')
            ->willReturn(false)
        ;

        self::assertThat($this->dataPersister->supports(new \stdClass(), []), self::equalTo(false));
        self::assertThat($this->dataPersister->supports(new MyEntity(), []), self::equalTo(false));
    }

    public function testCallsOnBeforeCreateAndOnAfterCreateOnPost() {
        $toPersist = new MyEntity();

        $this->dataPersister->expects(self::once())->method('beforeCreate')->willReturnArgument(0);
        $this->dataPersister->expects(self::once())->method('afterCreate');
        $this->dataPersister->expects(self::never())->method('beforeUpdate')->willReturnArgument(0);
        $this->dataPersister->expects(self::never())->method('afterUpdate');
        $this->dataPersister->expects(self::never())->method('beforeRemove');
        $this->dataPersister->expects(self::never())->method('afterRemove');

        $persistResult = $this->dataPersister->persist($toPersist, ['collection_operation_name' => 'post']);
        self::assertThat($persistResult, self::equalTo($toPersist));
    }

    public function testCallsOnBeforeUpdateAndOnAfterUpdateOnPatch() {
        $toPersist = new MyEntity();

        $this->dataPersister->expects(self::never())->method('beforeCreate')->willReturnArgument(0);
        $this->dataPersister->expects(self::never())->method('afterCreate');
        $this->dataPersister->expects(self::once())->method('beforeUpdate')->willReturnArgument(0);
        $this->dataPersister->expects(self::once())->method('afterUpdate');
        $this->dataPersister->expects(self::never())->method('beforeRemove');
        $this->dataPersister->expects(self::never())->method('afterRemove');

        $persistResult = $this->dataPersister->persist($toPersist, ['item_operation_name' => 'patch']);
        self::assertThat($persistResult, self::equalTo($toPersist));
    }

    public function testCallsOnBeforeRemoveAndOnAfterRemoveOnDelete() {
        $toPersist = new MyEntity();

        $this->dataPersister->expects(self::never())->method('beforeCreate')->willReturnArgument(0);
        $this->dataPersister->expects(self::never())->method('afterCreate');
        $this->dataPersister->expects(self::never())->method('beforeUpdate')->willReturnArgument(0);
        $this->dataPersister->expects(self::never())->method('afterUpdate');
        $this->dataPersister->expects(self::once())->method('beforeRemove');
        $this->dataPersister->expects(self::once())->method('afterRemove');

        $this->dataPersister->remove($toPersist, []);
    }

    public function testCallsCustomActionListeners() {
        $toPersist = new MyEntity();

        $this->closure->expects(self::exactly(2))
            ->method('call')
            ->with($toPersist)
            ->willReturnArgument(0)
        ;

        $persistResult = $this->dataPersister->persist($toPersist, ['item_operation_name' => self::ACTION_NAME]);
        self::assertThat($persistResult, self::equalTo($toPersist));
    }

    public function testCallsPropertyChangeListeners() {
        $oldData = new MyEntity();
        $oldData->name = 'old';
        $newData = new MyEntity();
        $newData->name = 'new';

        $this->parameterBag = new ParameterBag(['previous_data' => $oldData]);

        $this->closure->expects(self::exactly(2))
            ->method('call')
            ->with($newData)
            ->willReturnArgument(0)
        ;

        $persistResult = $this->dataPersister->persist($newData, []);
        self::assertThat($persistResult, self::equalTo($newData));
    }
}

class MyEntity extends BaseEntity {
    public string $name = 'test';
}
