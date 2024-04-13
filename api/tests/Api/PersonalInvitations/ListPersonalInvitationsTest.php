<?php

namespace App\Tests\Api\PersonalInvitations;

use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListPersonalInvitationsTest extends ECampApiTestCase {
    public function testListPersonalInvitationsIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/personal_invitations');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListPersonalInvitationsIsAllowedForLoggedInUserButFiltered() {
        /** @var User $user */
        $user = static::getFixture('user6invited');
        $client = static::createClientWithCredentials(['email' => $user->getProfile()->email]);
        $client->request('GET', '/personal_invitations');
        $this->assertResponseStatusCodeSame(200);
        $invitation = static::getFixture('campCollaboration6invitedWithUser');
        $this->assertJsonContains([
            'totalItems' => 1,
            '_links' => [
                'items' => [
                    ['href' => "/personal_invitations/{$invitation->getId()}"]
                ],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
    }
}
