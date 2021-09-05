<?php

namespace App\Tests\Entity;

use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class CampTest extends TestCase {
    private Camp $camp;
    private CampCollaboration $collaboration;

    public function setUp(): void {
        parent::setUp();
        $this->collaboration = new CampCollaboration();
        $this->collaboration->status = CampCollaboration::STATUS_ESTABLISHED;
        $this->collaboration->role = CampCollaboration::ROLE_MANAGER;
        $this->camp = new Camp();
        $this->camp->collaborations->add($this->collaboration);
    }

    public function testHasCollaboratorReturnsFalseWhenPassedNull() {
        // given

        // when
        $result = $this->camp->hasCollaborator(null);

        // then
        $this->assertEquals(false, $result);
    }

    public function testHasCollaboratorReturnsFalseWhenPassedString() {
        // given
        $this->collaboration->user = new User();
        $this->collaboration->user->username = 'test-user';

        // when
        $result = $this->camp->hasCollaborator('test-user');

        // then
        $this->assertEquals(false, $result);
    }

    public function testHasCollaboratorReturnsFalseWhenNoMatchingCollaboration() {
        // given
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn('idFromTest');
        $user->username = 'test-user';
        $this->collaboration->user = $user;

        $passedUser = $this->createMock(User::class);
        $passedUser->method('getId')->willReturn('wrongId');

        // when
        $result = $this->camp->hasCollaborator($passedUser);

        // then
        $this->assertEquals(false, $result);
    }

    /**
     * @dataProvider provideCollaborations
     *
     * @param null|array $allowedRoles
     */
    public function testHasCollaborator($status, $role, $expected, $allowedRoles = null) {
        // given
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn('idFromTest');
        $user->username = 'test-user';
        $this->collaboration->user = $user;
        $this->collaboration->status = $status;
        $this->collaboration->role = $role;

        $passedUser = $this->createMock(User::class);
        $passedUser->method('getId')->willReturn('idFromTest');

        // when
        $result = $allowedRoles ? $this->camp->hasCollaborator($passedUser, $allowedRoles) : $this->camp->hasCollaborator($passedUser);

        // then
        $this->assertEquals($expected, $result);
    }

    public function provideCollaborations() {
        return [
            [CampCollaboration::STATUS_INVITED, CampCollaboration::ROLE_GUEST, false],
            [CampCollaboration::STATUS_INVITED, CampCollaboration::ROLE_MEMBER, false],
            [CampCollaboration::STATUS_INVITED, CampCollaboration::ROLE_MANAGER, false],
            [CampCollaboration::STATUS_ESTABLISHED, CampCollaboration::ROLE_GUEST, true],
            [CampCollaboration::STATUS_ESTABLISHED, CampCollaboration::ROLE_MEMBER, true],
            [CampCollaboration::STATUS_ESTABLISHED, CampCollaboration::ROLE_MANAGER, true],
            [CampCollaboration::STATUS_INACTIVE, CampCollaboration::ROLE_GUEST, false],
            [CampCollaboration::STATUS_INACTIVE, CampCollaboration::ROLE_MEMBER, false],
            [CampCollaboration::STATUS_INACTIVE, CampCollaboration::ROLE_MANAGER, false],
            [CampCollaboration::STATUS_INVITED, CampCollaboration::ROLE_GUEST, false, [CampCollaboration::ROLE_MANAGER]],
            [CampCollaboration::STATUS_INVITED, CampCollaboration::ROLE_MEMBER, false, [CampCollaboration::ROLE_MANAGER]],
            [CampCollaboration::STATUS_INVITED, CampCollaboration::ROLE_MANAGER, false, [CampCollaboration::ROLE_MANAGER]],
            [CampCollaboration::STATUS_ESTABLISHED, CampCollaboration::ROLE_GUEST, false, [CampCollaboration::ROLE_MANAGER]],
            [CampCollaboration::STATUS_ESTABLISHED, CampCollaboration::ROLE_MEMBER, false, [CampCollaboration::ROLE_MANAGER]],
            [CampCollaboration::STATUS_ESTABLISHED, CampCollaboration::ROLE_MANAGER, true, [CampCollaboration::ROLE_MANAGER]],
            [CampCollaboration::STATUS_INACTIVE, CampCollaboration::ROLE_GUEST, false, [CampCollaboration::ROLE_MANAGER]],
            [CampCollaboration::STATUS_INACTIVE, CampCollaboration::ROLE_MEMBER, false, [CampCollaboration::ROLE_MANAGER]],
            [CampCollaboration::STATUS_INACTIVE, CampCollaboration::ROLE_MANAGER, false, [CampCollaboration::ROLE_MANAGER]],
            [CampCollaboration::STATUS_INVITED, CampCollaboration::ROLE_GUEST, false, [CampCollaboration::ROLE_MANAGER, CampCollaboration::ROLE_MEMBER]],
            [CampCollaboration::STATUS_INVITED, CampCollaboration::ROLE_MEMBER, false, [CampCollaboration::ROLE_MANAGER, CampCollaboration::ROLE_MEMBER]],
            [CampCollaboration::STATUS_INVITED, CampCollaboration::ROLE_MANAGER, false, [CampCollaboration::ROLE_MANAGER, CampCollaboration::ROLE_MEMBER]],
            [CampCollaboration::STATUS_ESTABLISHED, CampCollaboration::ROLE_GUEST, false, [CampCollaboration::ROLE_MANAGER, CampCollaboration::ROLE_MEMBER]],
            [CampCollaboration::STATUS_ESTABLISHED, CampCollaboration::ROLE_MEMBER, true, [CampCollaboration::ROLE_MANAGER, CampCollaboration::ROLE_MEMBER]],
            [CampCollaboration::STATUS_ESTABLISHED, CampCollaboration::ROLE_MANAGER, true, [CampCollaboration::ROLE_MANAGER, CampCollaboration::ROLE_MEMBER]],
            [CampCollaboration::STATUS_INACTIVE, CampCollaboration::ROLE_GUEST, false, [CampCollaboration::ROLE_MANAGER, CampCollaboration::ROLE_MEMBER]],
            [CampCollaboration::STATUS_INACTIVE, CampCollaboration::ROLE_MEMBER, false, [CampCollaboration::ROLE_MANAGER, CampCollaboration::ROLE_MEMBER]],
            [CampCollaboration::STATUS_INACTIVE, CampCollaboration::ROLE_MANAGER, false, [CampCollaboration::ROLE_MANAGER, CampCollaboration::ROLE_MEMBER]],
        ];
    }
}
