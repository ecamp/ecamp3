<?php

namespace App\Tests\DataPersister\Util;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataPersister\Util\CustomActionListener;
use App\DataPersister\Util\DataPersisterObservable;
use App\DataPersister\Util\PropertyChangeListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @internal
 */
class DataPersisterObservableTest extends TestCase {
    public const ACTION_NAME = 'myAction';

    private MockObject|ContextAwareDataPersisterInterface $contextAwareDataPersister;
    private DataPersisterObservable $dataPersisterObservable;
    private MockObject|MyClosure $closure;
    private CustomActionListener $actionListener;
    private ParameterBag $parameterBag;

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

        $this->closure = $this->createMock(MyClosure::class);

        $this->actionListener = CustomActionListener::of(
            self::ACTION_NAME,
            fn ($data) => $this->closure->call($data),
            fn ($data) => $this->closure->call($data)
        );
    }

    public function testDelegateSupports() {
        $this->contextAwareDataPersister->expects(self::exactly(2))
            ->method('supports')
            ->willReturn(true, false)
        ;

        self::assertThat($this->dataPersisterObservable->supports(new stdClass(), []), self::equalTo(true));
        self::assertThat($this->dataPersisterObservable->supports(new stdClass(), []), self::equalTo(false));
    }

    public function testCallCreateCallbacksOnPost() {
        $toPersist = new stdClass();

        $this->closure->expects(self::exactly(2))
            ->method('call')
            ->with($toPersist)
            ->willReturnArgument(0)
        ;
        $this->contextAwareDataPersister->expects(self::once())
            ->method('persist')
            ->with($toPersist)
            ->willReturnArgument(0)
        ;
        $this->dataPersisterObservable->onBeforeCreate(fn ($data) => $this->closure->call($data));
        $this->dataPersisterObservable->onAfterCreate(fn ($data) => $this->closure->call($data));

        $this->dataPersisterObservable->persist($toPersist, ['collection_operation_name' => 'post']);
    }

    public function testCallUpdateCallbacksOnPatch() {
        $toPersist = new stdClass();

        $this->closure->expects(self::exactly(2))
            ->method('call')
            ->with($toPersist)
            ->willReturnArgument(0)
        ;
        $this->contextAwareDataPersister->expects(self::once())
            ->method('persist')
            ->with($toPersist)
            ->willReturnArgument(0)
        ;
        $this->dataPersisterObservable->onBeforeUpdate(fn ($data) => $this->closure->call($data));
        $this->dataPersisterObservable->onAfterUpdate(fn ($data) => $this->closure->call($data));

        $this->dataPersisterObservable->persist($toPersist, ['item_operation_name' => 'patch']);
    }

    public function testCallRemoveCallbacksOnRemove() {
        $toRemove = new stdClass();

        $this->closure->expects(self::exactly(2))
            ->method('call')
            ->with($toRemove)
            ->willReturnArgument(0)
        ;
        $this->contextAwareDataPersister->expects(self::once())
            ->method('persist')
            ->with($toRemove)
            ->willReturnArgument(0)
        ;
        $this->contextAwareDataPersister->expects(self::never())->method('remove');
        $this->dataPersisterObservable->onBeforeRemove(fn ($data) => $this->closure->call($data));
        $this->dataPersisterObservable->onAfterRemove(fn ($data) => $this->closure->call($data));

        $this->dataPersisterObservable->remove($toRemove, []);
    }

    public function testRemoveObjectIfOnBeforeRemoveReturnsNull() {
        $toRemove = new stdClass();

        $this->closure->expects(self::exactly(2))
            ->method('call')
            ->withConsecutive([$toRemove], [$toRemove])
            ->willReturn(null, null)
        ;
        $this->contextAwareDataPersister->expects(self::never())->method('persist');
        $this->contextAwareDataPersister->expects(self::once())
            ->method('remove')
            ->with($toRemove)
        ;
        $this->dataPersisterObservable->onBeforeRemove(fn ($data) => $this->closure->call($data));
        $this->dataPersisterObservable->onAfterRemove(fn ($data) => $this->closure->call($data));

        $this->dataPersisterObservable->remove($toRemove, []);
    }

    public function testCallCustomActionListenersForItemOperation() {
        $toPersist = new stdClass();

        $this->closure->expects(self::exactly(2))
            ->method('call')
            ->with($toPersist)
            ->willReturnArgument(0)
        ;
        $this->contextAwareDataPersister->expects(self::once())
            ->method('persist')
            ->with($toPersist)
            ->willReturnArgument(0)
        ;
        $this->dataPersisterObservable->onCustomAction($this->actionListener);

        $this->dataPersisterObservable->persist($toPersist, ['item_operation_name' => self::ACTION_NAME]);
    }

    public function testCallCustomActionListenersForItemOperationTwiceIfRegisteredTwice() {
        $toPersist = new stdClass();

        $this->closure->expects(self::exactly(4))
            ->method('call')
            ->with($toPersist)
            ->willReturnArgument(0)
        ;
        $this->contextAwareDataPersister->expects(self::once())
            ->method('persist')
            ->with($toPersist)
            ->willReturnArgument(0)
        ;
        $this->dataPersisterObservable->onCustomAction($this->actionListener);
        $this->dataPersisterObservable->onCustomAction($this->actionListener);

        $this->dataPersisterObservable->persist($toPersist, ['item_operation_name' => self::ACTION_NAME]);
    }

    public function testCallCustomActionListenersForCollectionOperation() {
        $toPersist = new stdClass();

        $this->closure->expects(self::exactly(2))
            ->method('call')
            ->with($toPersist)
            ->willReturnArgument(0)
        ;
        $this->contextAwareDataPersister->expects(self::once())
            ->method('persist')
            ->with($toPersist)
            ->willReturnArgument(0)
        ;
        $this->dataPersisterObservable->onCustomAction($this->actionListener);

        $this->dataPersisterObservable->persist($toPersist, ['collection_operation_name' => self::ACTION_NAME]);
    }

    public function testCallNoOperationCallbacksIfOperationNameDoesNotMatch() {
        $toPersist = new stdClass();
        $this->closure->expects(self::never())->method('call');
        $this->contextAwareDataPersister->expects(self::exactly(3))
            ->method('persist')
            ->with($toPersist)
            ->willReturnArgument(0)
        ;

        $this->dataPersisterObservable->onBeforeCreate(fn ($data) => $this->closure->call($data));
        $this->dataPersisterObservable->onAfterCreate(fn ($data) => $this->closure->call($data));
        $this->dataPersisterObservable->onBeforeUpdate(fn ($data) => $this->closure->call($data));
        $this->dataPersisterObservable->onAfterUpdate(fn ($data) => $this->closure->call($data));
        $this->dataPersisterObservable->onBeforeRemove(fn ($data) => $this->closure->call($data));
        $this->dataPersisterObservable->onAfterRemove(fn ($data) => $this->closure->call($data));
        $this->dataPersisterObservable->onCustomAction($this->actionListener);

        $this->dataPersisterObservable->persist($toPersist, []);
        $this->dataPersisterObservable->persist($toPersist, ['item_operation_name' => self::ACTION_NAME.'2']);
        $this->dataPersisterObservable->persist($toPersist, ['collection_operation_name' => self::ACTION_NAME.'2']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testNotCallPropertyChangeListenerIfDataWasNullBefore() {
        $toPersist = new stdClass();

        $this->parameterBag = new ParameterBag(['previous_data' => null]);
        $propertyChangeListener = PropertyChangeListener::of(
            extractProperty: fn ($data) => $data->name,
            beforeAction: fn ($data) => $this->closure->call($data),
            afterAction: fn ($data) => $this->closure->call($data),
        );
        $this->closure->expects(self::never())->method('call');
        $this->dataPersisterObservable->onPropertyChange($propertyChangeListener);

        $this->dataPersisterObservable->persist($toPersist, []);
    }

    /**
     * @throws \ReflectionException
     */
    public function testThrowErrorIfExtractPropertyFails() {
        $toPersist = new stdClass();

        $this->parameterBag = new ParameterBag(['previous_data' => $toPersist]);
        $propertyChangeListener = PropertyChangeListener::of(
            extractProperty: fn ($data) => $data->name,
            afterAction: fn ($data) => $this->closure->call($data)
        );
        $this->closure->expects(self::never())->method('call');
        $this->dataPersisterObservable->onPropertyChange($propertyChangeListener);

        $this->expectWarning();
        $this->dataPersisterObservable->persist($toPersist, []);
    }

    /**
     * @throws \ReflectionException
     */
    public function testNotCallPropertyChangeListenerIfPropertyDidNotChange() {
        $toPersist = new stdClass();
        $toPersist->name = null;

        $this->parameterBag = new ParameterBag(['previous_data' => $toPersist]);
        $propertyChangeListener = PropertyChangeListener::of(
            extractProperty: fn ($data) => $data->name,
            beforeAction: fn ($data) => $this->closure->call($data),
            afterAction: fn ($data) => $this->closure->call($data),
        );
        $this->closure->expects(self::never())->method('call');
        $this->dataPersisterObservable->onPropertyChange($propertyChangeListener);

        $this->dataPersisterObservable->persist($toPersist, []);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCallPropertyChangeListenerIfPropertyDidChange() {
        $oldData = new stdClass();
        $oldData->name = null;
        $newData = new stdClass();
        $newData->name = 'test';

        $this->parameterBag = new ParameterBag(['previous_data' => $oldData]);
        $propertyChangeListener = PropertyChangeListener::of(
            extractProperty: fn ($data) => $data->name,
            beforeAction: fn ($data) => $this->closure->call($data),
            afterAction: fn ($data) => $this->closure->call($data),
        );
        $this->contextAwareDataPersister->expects(self::exactly(1))
            ->method('persist')
            ->willReturnArgument(0)
        ;
        $this->closure->expects(self::exactly(2))
            ->method('call')
            ->willReturnOnConsecutiveCalls($newData, null)
        ;
        $this->dataPersisterObservable->onPropertyChange($propertyChangeListener);

        $this->dataPersisterObservable->persist($newData, []);
    }
}
