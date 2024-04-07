<?php

namespace App\Tests\Api\PersonalInvitations;

use App\Entity\CampCollaboration;
use App\Entity\Profile;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeletePersonalInvitationTest extends ECampApiTestCase {
    public function testDeleteIsNotAllowed() {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = static::getFixture('campCollaboration2invitedCampUnrelated');

        /** @var Profile $profile */
        $profile = static::getFixture('profile6invited');
        static::createClientWithCredentials(['email' => $profile->email])->request('DELETE', '/invitations/'.$campCollaboration->getId());

        $this->assertResponseStatusCodeSame(404);
    }
}
