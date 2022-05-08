<?php

namespace App\Tests\Entity;

use App\Entity\CampCollaboration;
use App\Entity\MaterialList;
use App\Entity\User;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class MaterialListTest extends TestCase {
    public const USER_DISPLAY_NAME = 'DISPLAYNAME';
    private MockObject|User $user;
    private CampCollaboration $campCollaboration;

    protected function setUp(): void {
        $this->user = $this->createMock(User::class);
        $this->user->method('getDisplayName')->willReturn(self::USER_DISPLAY_NAME);
        $this->campCollaboration = new CampCollaboration();
    }

    public function testNameWhenNoCampCollaboration() {
        $materialList = new MaterialList();
        $materialList->name = 'test';

        assertThat($materialList->getName(), equalTo($materialList->name));
    }

    public function testNameWhenCampCollaborationButNameIsSet() {
        $materialList = new MaterialList();
        $materialList->name = 'test';
        $this->campCollaboration->inviteEmail = 'test@mail.com';
        $materialList->campCollaboration = $this->campCollaboration;

        assertThat($materialList->getName(), equalTo($materialList->name));
    }

    public function testNameWhenCampCollaborationWithUserButNameIsSet() {
        $materialList = new MaterialList();
        $materialList->name = 'test';
        $this->campCollaboration->user = $this->user;
        $materialList->campCollaboration = $this->campCollaboration;

        assertThat($materialList->getName(), equalTo($materialList->name));
    }

    public function testNameWhenNoCampCollaborationAndNameNull() {
        $materialList = new MaterialList();
        $materialList->name = null;

        assertThat($materialList->getName(), equalTo('NoName'));
    }

    public function testNameWhenCampCollaborationAndNameNull() {
        $materialList = new MaterialList();
        $materialList->name = null;
        $this->campCollaboration->inviteEmail = 'test@mail.com';
        $materialList->campCollaboration = $this->campCollaboration;

        assertThat($materialList->getName(), equalTo($this->campCollaboration->inviteEmail));
    }

    public function testNameWhenCampCollaborationWithUserAndNameNull() {
        $materialList = new MaterialList();
        $materialList->name = null;
        $this->campCollaboration->user = $this->user;
        $materialList->campCollaboration = $this->campCollaboration;

        assertThat($materialList->getName(), equalTo(self::USER_DISPLAY_NAME));
    }
}
