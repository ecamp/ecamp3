<?php

namespace App\Tests\Api\Camps;

use App\Entity\Camp;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadCampTest extends ECampApiTestCase {
    public function testGetSingleCampIsDeniedForAnonymousUser() {
        $camp = static::getFixture('camp1');
        static::createBasicClient()->request('GET', '/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleCampIsDeniedForUnrelatedUser() {
        $camp = static::getFixture('campUnrelated');
        static::createClientWithCredentials()->request('GET', '/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleCampIsDeniedForInactiveCollaborator() {
        $camp = static::getFixture('campUnrelated');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
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
        $camp = static::getFixture('camp1');
        $user = static::getFixture('user3guest');
        $response = static::createClientWithCredentials(['email' => $user->getEmail()])->request('GET', '/camps/'.$camp->getId());
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
                'categories' => ['href' => "/camps/{$camp->getId()}/categories"],
            ],
        ]);

        $responseArray = $response->toArray();
        $period1 = static::getFixture('period1');
        $period2 = static::getFixture('period2');
        $this->assertEquals("/periods/{$period2->getId()}/days", $responseArray['_embedded']['periods'][0]['_links']['days']['href']);
        $this->assertEquals("/periods/{$period1->getId()}/days", $responseArray['_embedded']['periods'][1]['_links']['days']['href']);
    }

    public function testGetSingleCampIsAllowedForMember() {
        /** @var Camp $camp */
        $camp = static::getFixture('camp1');
        $user = static::getFixture('user2member');
        static::createClientWithCredentials(['email' => $user->getEmail()])->request('GET', '/camps/'.$camp->getId());
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
                'categories' => ['href' => "/camps/{$camp->getId()}/categories"],
            ],
        ]);
    }

    public function testGetSingleCampIsAllowedForManager() {
        /** @var Camp $camp */
        $camp = static::getFixture('camp1');
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
                'categories' => ['href' => "/camps/{$camp->getId()}/categories"],
            ],
        ]);
    }

    public function testGetSinglePrototypeCampIsAllowedForUnrelatedUser() {
        /** @var Camp $camp */
        $camp = static::getFixture('campPrototype');
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
        $camp = static::getFixture('campPrototype');

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
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials()->request('GET', '/camps/'.$camp->getId());

        $this->assertResponseStatusCodeSame(200);
        $this->assertArrayNotHasKey('campPrototypeId', $response->toArray());
    }

    public function testGetSingleCampEmbedsCampCollaborationsAndItsUser() {
        /** @var Camp $camp */
        $camp = static::getFixture('camp1');
        $user = static::getFixture('user2member');
        static::createClientWithCredentials(['email' => $user->getEmail()])->request('GET', '/camps/'.$camp->getId());
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
