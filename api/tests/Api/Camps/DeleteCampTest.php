<?php

namespace App\Tests\Api\Camps;

use App\Repository\CampRepository;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteCampTest extends ECampApiTestCase {
    public function testDeleteCampIsDeniedToAnonymousUser() {
        $camp = static::$fixtures['camp1'];
        static::createClient()->request('DELETE', '/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteCampIsDeniedForUnrelatedUser() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4']->getUsername()])
            ->request('DELETE', '/camps/'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteCampIsDeniedForOtherwiseUnrelatedCreator() {
        $camp = static::$fixtures['camp2'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2']->getUsername()])
            ->request('DELETE', '/camps/'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteCampIsDeniedForCollaboratorThatIsNotOwner() {
        $camp = static::$fixtures['camp2'];
        static::createClientWithCredentials()->request('DELETE', '/camps/'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteCampIsAllowedForCampOwner() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('DELETE', '/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(static::getContainer()->get(CampRepository::class)->findOneBy(['id' => $camp->getId()]));
    }
}
