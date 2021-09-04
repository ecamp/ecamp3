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

    public function testGetSingleCampIsAllowedForCollaborator() {
        /** @var Camp $camp */
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('GET', '/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $camp->getId(),
            'name' => $camp->name,
            'title' => $camp->title,
            'motto' => $camp->motto,
            'addressName' => $camp->addressName,
            'addressStreet' => $camp->addressStreet,
            'addressZipcode' => $camp->addressZipcode,
            'addressCity' => $camp->addressCity,
            //'role' => 'manager',
            'isPrototype' => false,
            '_links' => [
                'creator' => ['href' => $this->getIriFor('user2')],
                'activities' => ['href' => '/activities?camp=/camps/'.$camp->getId()],
                'materialLists' => ['href' => '/material_lists?camp=/camps/'.$camp->getId()],
                'campCollaborations' => ['href' => '/camp_collaborations?camp=/camps/'.$camp->getId()],
                'periods' => ['href' => '/periods?camp=/camps/'.$camp->getId()],
                'categories' => ['href' => '/categories?camp=/camps/'.$camp->getId()],
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
        /** @var Camp $camp */
        $camp = static::$fixtures['camp1'];
        static::createClientWithAdminCredentials()->request('GET', '/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $camp->getId(),
            'name' => $camp->name,
            'title' => $camp->title,
            'motto' => $camp->motto,
            'addressName' => $camp->addressName,
            'addressStreet' => $camp->addressStreet,
            'addressZipcode' => $camp->addressZipcode,
            'addressCity' => $camp->addressCity,
            //'role' => 'manager',
            'isPrototype' => false,
            '_links' => [
                'creator' => ['href' => $this->getIriFor('user2')],
                'activities' => ['href' => '/activities?camp=/camps/'.$camp->getId()],
                'materialLists' => ['href' => '/material_lists?camp=/camps/'.$camp->getId()],
                'campCollaborations' => ['href' => '/camp_collaborations?camp=/camps/'.$camp->getId()],
                'periods' => ['href' => '/periods?camp=/camps/'.$camp->getId()],
                'categories' => ['href' => '/categories?camp=/camps/'.$camp->getId()],
            ],
        ]);
    }
}
