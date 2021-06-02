<?php

namespace App\Tests\Api\Camps;

use App\Entity\Camp;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadCampTest extends ECampApiTestCase {
    public function testGetSingleCampIsDeniedToAnonymousUser() {
        $camp = static::$fixtures['camp1'];
        static::createClient()->request('GET', '/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleCampIsDeniedForUnrelatedUser() {
        $camp2 = static::$fixtures['camp2'];
        static::createClientWithCredentials()->request('GET', '/camps/'.$camp2->getId());
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleCampIsAllowedForOwnedCamp() {
        /** @var Camp $camp */
        $camp = static::$fixtures['camp1'];
        $userIri = $this->getIriConverter()->getIriFromItem(static::$fixtures['user1']);
        $user2Iri = $this->getIriConverter()->getIriFromItem(static::$fixtures['user2']);
        static::createClientWithCredentials()->request('GET', '/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'name' => $camp->getName(),
            'title' => $camp->getTitle(),
            'motto' => $camp->getMotto(),
            'addressName' => $camp->getAddressName(),
            'addressStreet' => $camp->getAddressStreet(),
            'addressZipcode' => $camp->getAddressZipcode(),
            'addressCity' => $camp->getAddressCity(),
            '_links' => [
                'owner' => ['href' => $userIri],
                'creator' => ['href' => $user2Iri],
            ],
        ]);
    }

    public function testGetSingleCampDoesntExposeCampPrototypeId() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials()->request('GET', '/camps/'.$camp->getId());

        $this->assertResponseStatusCodeSame(200);
        $this->assertArrayNotHasKey('campPrototypeId', $response->toArray());
    }

    public function testGetSingleCampIsAllowedForAdmin() {
        $camp = static::$fixtures['camp1'];
        $userIri = $this->getIriConverter()->getIriFromItem(static::$fixtures['user1']);
        $user2Iri = $this->getIriConverter()->getIriFromItem(static::$fixtures['user2']);
        static::createClientWithAdminCredentials()->request('GET', '/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'name' => $camp->getName(),
            'title' => $camp->getTitle(),
            'motto' => $camp->getMotto(),
            'addressName' => $camp->getAddressName(),
            'addressStreet' => $camp->getAddressStreet(),
            'addressZipcode' => $camp->getAddressZipcode(),
            'addressCity' => $camp->getAddressCity(),
            '_links' => [
                'owner' => ['href' => $userIri],
                'creator' => ['href' => $user2Iri],
            ],
        ]);
    }
}
