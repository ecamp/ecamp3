<?php

namespace App\Tests\DataPersister;

use App\DataPersister\CampDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

/**
 * @internal
 */
class CampDataPersisterTest extends TestCase {
    private CampDataPersister $dataPersister;
    private MockObject|Security $security;
    private MockObject|EntityManagerInterface $em;
    private Camp $camp;

    protected function setUp(): void {
        $this->camp = new Camp();

        $this->security = $this->createMock(Security::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->dataPersister = new CampDataPersister($dataPersisterObservable, $this->security, $this->em);
    }

    public function testSetsCreatorAndOwnerOnCreate() {
        // given
        $user = new User();
        $this->security->method('getUser')->willReturn($user);

        // when
        /** @var Camp $data */
        $data = $this->dataPersister->beforeCreate($this->camp);

        // then
        $this->assertEquals($user, $data->creator);
        $this->assertEquals($user, $data->owner);
    }

    public function testCreatesCampCollaborationAfterCreate() {
        // given
        $user = new User();
        $this->security->method('getUser')->willReturn($user);

        // then
        $this->em->expects($this->once())->method('persist')->will($this->returnCallback(function ($object) use ($user) {
            $this->assertInstanceOf(CampCollaboration::class, $object);
            $campCollaboration = $object;
            $this->assertEquals($user, $campCollaboration->user);
            $this->assertEquals($this->camp, $campCollaboration->camp);
            $this->assertEquals(CampCollaboration::STATUS_ESTABLISHED, $campCollaboration->status);
            $this->assertEquals(CampCollaboration::ROLE_MANAGER, $campCollaboration->role);
            $this->assertContains($campCollaboration, $this->camp->collaborations);
        }));

        // when
        $this->dataPersister->afterCreate($this->camp);
    }
}
