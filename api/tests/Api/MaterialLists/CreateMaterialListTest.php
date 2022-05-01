<?php

namespace App\Tests\Api\MaterialLists;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\MaterialList;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateMaterialListTest extends ECampApiTestCase {
    public function testCreateMaterialListIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateMaterialListIsNotPossibleForUnrelatedUserBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
            ->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreateMaterialListIsNotPossibleForInactiveCollaboratorBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreateMaterialListIsDeniedForGuest() {
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->getUsername()])
            ->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateMaterialListIsAllowedForMember() {
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateMaterialListIsAllowedForManager() {
        static::createClientWithCredentials()->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateMaterialListInCampPrototypeIsDeniedForUnrelatedUser() {
        static::createClientWithCredentials()->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload([
            'camp' => $this->getIriFor('campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateMaterialListValidatesMissingCamp() {
        static::createClientWithCredentials()->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload([], ['camp'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'camp',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateMaterialListDisallowsSettingCampCollaboration() {
        static::createClientWithCredentials()
            ->request(
                'POST',
                '/material_lists',
                [
                    'json' => $this->getExampleWritePayload([
                        'campCollaboration' => $this->getIriFor('campCollaboration1manager'),
                    ]),
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("campCollaboration" is unknown).',
        ]);
    }

    public function testCreateMaterialListValidatesMissingName() {
        static::createClientWithCredentials()->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload([], ['name'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'name',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateMaterialListValidatesTooLongName() {
        static::createClientWithCredentials()
            ->request(
                'POST',
                '/material_lists',
                [
                    'json' => $this->getExampleWritePayload([
                        'name' => 'a very long name with more than a',
                    ]),
                ]
            )
    ;

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'name',
                    'message' => 'This value is too long. It should have 32 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateMaterialListTrimsName() {
        static::createClientWithCredentials()
            ->request(
                'POST',
                '/material_lists',
                [
                    'json' => $this->getExampleWritePayload([
                        'name' => ' Something ',
                    ]),
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'name' => 'Something',
        ]);
    }

    public function testCreateMaterialListCleansHtml() {
        static::createClientWithCredentials()
            ->request(
                'POST',
                '/material_lists',
                [
                    'json' => $this->getExampleWritePayload([
                        'name' => ' Some<script>alert(1)</script>thing ',
                    ]),
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'name' => 'Something',
        ]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            MaterialList::class,
            OperationType::COLLECTION,
            'post',
            array_merge(['camp' => $this->getIriFor('camp1')], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            MaterialList::class,
            OperationType::ITEM,
            'get',
            $attributes,
            ['camp'],
            $except
        );
    }
}
