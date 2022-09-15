<?php

namespace App\Tests\Api\Camps;

use App\Repository\CampRepository;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteCampTest extends ECampApiTestCase {
    public function testDeleteCampIsDeniedForAnonymousUser() {
        $camp = static::$fixtures['camp1'];
        static::createBasicClient()->request('DELETE', '/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteCampIsDeniedForUnrelatedUser() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
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
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
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

    public function testDeletePrototypeCampIsDeniedForUnrelatedUser() {
        $camp = static::$fixtures['campPrototype'];
        static::createClientWithCredentials()->request('DELETE', '/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteCampAlsoDeletesContentNodes() {
        $client = static::createClientWithCredentials();
        // Disable resetting the database between the two requests
        $client->disableReboot();

        $client->request('DELETE', $this->getIriFor(static::$fixtures['camp1']));
        $this->assertResponseStatusCodeSame(204);

        $client->request('GET', $this->getIriFor(static::$fixtures['activity1']));
        $this->assertResponseStatusCodeSame(404);

        $client->request('GET', $this->getIriFor(static::$fixtures['columnLayout1']));
        $this->assertResponseStatusCodeSame(404);

        $client->request('GET', $this->getIriFor(static::$fixtures['category1']));
        $this->assertResponseStatusCodeSame(404);

        $client->request('GET', $this->getIriFor(static::$fixtures['columnLayout2']));
        $this->assertResponseStatusCodeSame(404);
    }
}
