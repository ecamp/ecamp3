<?php

namespace App\Tests\Api\Camps;

use App\Entity\Camp;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadCampTest extends ECampApiTestCase {
    public function testGetSingleCampIsDeniedForAnonymousUser() {
        $camp = static::$fixtures['camp1'];
        static::createBasicClient()->request('GET', '/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleCampIsDeniedForUnrelatedUser() {
        $camp = static::$fixtures['campUnrelated'];
        static::createClientWithCredentials()->request('GET', '/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleCampIsDeniedForInactiveCollaborator() {
        $camp = static::$fixtures['campUnrelated'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('GET', '/camps/'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleCampIsAllowedForGuest() {
        /** @var Camp $camp */
        $camp = static::$fixtures['camp1'];
        $user = static::$fixtures['user3guest'];
        static::createClientWithCredentials(['username' => $user->getUsername()])->request('GET', '/camps/'.$camp->getId());
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
            // 'role' => 'guest',
            'isPrototype' => false,
            '_links' => [
                'creator' => ['href' => $this->getIriFor('user2member')],
                'activities' => ['href' => '/activities?camp=%2Fcamps%2F'.$camp->getId()],
                'materialLists' => ['href' => '/material_lists?camp=%2Fcamps%2F'.$camp->getId()],
                'campCollaborations' => ['href' => '/camp_collaborations?camp=%2Fcamps%2F'.$camp->getId()],
                'periods' => ['href' => '/periods?camp=%2Fcamps%2F'.$camp->getId()],
                'categories' => ['href' => '/categories?camp=%2Fcamps%2F'.$camp->getId()],
            ],
        ]);
    }

    public function testGetSingleCampIsAllowedForMember() {
        /** @var Camp $camp */
        $camp = static::$fixtures['camp1'];
        $user = static::$fixtures['user2member'];
        static::createClientWithCredentials(['username' => $user->getUsername()])->request('GET', '/camps/'.$camp->getId());
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
            // 'role' => 'member',
            'isPrototype' => false,
            '_links' => [
                'creator' => ['href' => $this->getIriFor('user2member')],
                'activities' => ['href' => '/activities?camp=%2Fcamps%2F'.$camp->getId()],
                'materialLists' => ['href' => '/material_lists?camp=%2Fcamps%2F'.$camp->getId()],
                'campCollaborations' => ['href' => '/camp_collaborations?camp=%2Fcamps%2F'.$camp->getId()],
                'periods' => ['href' => '/periods?camp=%2Fcamps%2F'.$camp->getId()],
                'categories' => ['href' => '/categories?camp=%2Fcamps%2F'.$camp->getId()],
            ],
        ]);
    }

    public function testGetSingleCampIsAllowedForManager() {
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
            // 'role' => 'manager',
            'isPrototype' => false,
            '_links' => [
                'creator' => ['href' => $this->getIriFor('user2member')],
                'activities' => ['href' => '/activities?camp=%2Fcamps%2F'.$camp->getId()],
                'materialLists' => ['href' => '/material_lists?camp=%2Fcamps%2F'.$camp->getId()],
                'campCollaborations' => ['href' => '/camp_collaborations?camp=%2Fcamps%2F'.$camp->getId()],
                'periods' => ['href' => '/periods?camp=%2Fcamps%2F'.$camp->getId()],
                'categories' => ['href' => '/categories?camp=%2Fcamps%2F'.$camp->getId()],
            ],
        ]);
    }

    public function testGetSinglePrototypeCampIsAllowedForUnrelatedUser() {
        /** @var Camp $camp */
        $camp = static::$fixtures['campPrototype'];
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
            'isPrototype' => true,
            '_links' => [
                'creator' => [],
            ],
        ]);
    }

    public function testGetSinglePrototypeCampIsAllowedForUnrelatedUserEvenWithoutAnyCollaborations() {
        /** @var Camp $camp */
        $camp = static::$fixtures['campPrototype'];

        // Precondition: no collaborations on the camp.
        // This is to make sure a left join from camp to collaborations is used.
        $this->assertEmpty($camp->collaborations);

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
            'isPrototype' => true,
            '_links' => [
                'creator' => [],
            ],
        ]);
    }

    public function testGetSingleCampDoesntExposeCampPrototypeId() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials()->request('GET', '/camps/'.$camp->getId());

        $this->assertResponseStatusCodeSame(200);
        $this->assertArrayNotHasKey('campPrototypeId', $response->toArray());
    }

    public function testGetSingleCampEmbedsCampCollaborationsAndItsUser() {
        /** @var Camp $camp */
        $camp = static::$fixtures['camp1'];
        $user = static::$fixtures['user2member'];
        static::createClientWithCredentials(['username' => $user->getUsername()])->request('GET', '/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertMatchesJsonSchema([
            'type' => 'object',
            'required' => ['_embedded'],
            'properties' => [
                '_embedded' => [
                    'type' => 'object',
                    'required' => ['campCollaborations'],
                    'properties' => [
                        'campCollaborations' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'required' => ['_embedded'],
                                'properties' => [
                                    '_embedded' => [
                                        'type' => 'object',
                                        'required' => ['user'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
