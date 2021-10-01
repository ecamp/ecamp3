<?php

namespace App\Tests\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataPersister\CampDataPersister;
use App\Entity\Camp;
use App\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

/**
 * @internal
 */
class CampDataPersisterTest extends TestCase {
    private CampDataPersister $dataPersister;
    private MockObject|ContextAwareDataPersisterInterface $decoratedMock;
    private Security|MockObject $security;
    private Camp $camp;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(ContextAwareDataPersisterInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->camp = new Camp();

        $this->dataPersister = new CampDataPersister($this->decoratedMock, $this->security);
    }

    public function testDelegatesSupportCheckToDecorated() {
        $this->decoratedMock
            ->expects($this->exactly(2))
            ->method('supports')
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        $this->assertTrue($this->dataPersister->supports($this->camp, []));
        $this->assertFalse($this->dataPersister->supports($this->camp, []));
    }

    public function testDoesNotSupportNonCamp() {
        $this->decoratedMock
            ->method('supports')
            ->willReturn(true)
        ;

        $this->assertFalse($this->dataPersister->supports([], []));
    }

    public function testDelegatesPersistToDecorated() {
        // given
        $this->decoratedMock->expects($this->once())
            ->method('persist')
        ;

        // when
        $this->dataPersister->persist($this->camp, []);

        // then
    }

    public function testSetsCreatorAndOwnerOnCreate() {
        // given
        $user = new User();
        $this->security->method('getUser')->willReturn($user);
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var Camp $data */
        $data = $this->dataPersister->persist($this->camp, ['collection_operation_name' => 'post']);

        // then
        $this->assertEquals($user, $data->creator);
        $this->assertEquals($user, $data->owner);
    }

    public function testDoesNotSetCreatorAndOwnerOnUpdate() {
        // given
        $user = new User();
        $this->security->method('getUser')->willReturn($user);
        $this->decoratedMock->expects($this->once())->method('persist')->willReturnArgument(0);

        // when
        /** @var Camp $data */
        $data = $this->dataPersister->persist($this->camp, ['item_operation_name' => 'patch']);

        // then
        $this->assertNotEquals($user, $data->creator);
        $this->assertNotEquals($user, $data->owner);
    }
}
