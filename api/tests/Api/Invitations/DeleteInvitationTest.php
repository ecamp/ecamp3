<?php

namespace App\Tests\Api\Invitations;

use App\DTO\Invitation;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteInvitationTest extends ECampApiTestCase {
    public function testDeleteIsNotAllowed() {
        /** @var Invitation $invitation */
        $invitation = static::$fixtures['campCollaboration2invitedCampUnrelated'];
        static::createClientWithCredentials()->request('DELETE', '/invitations/'.$invitation->inviteKey);

        $this->assertResponseStatusCodeSame(405);
    }
}
