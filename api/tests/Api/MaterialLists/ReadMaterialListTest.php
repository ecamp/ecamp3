<?php

namespace App\Tests\Api\MaterialLists;

use App\Entity\MaterialList;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadMaterialListTest extends ECampApiTestCase {
    public function testGetSingleMaterialListIsDeniedForAnonymousUser() {
        /** @var MaterialList $materialList */
        $materialList = static::$fixtures['materialList1'];
        static::createBasicClient()->request('GET', '/material_lists/'.$materialList->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleMaterialListIsDeniedForUnrelatedUser() {
        /** @var MaterialList $materialList */
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('GET', '/material_lists/'.$materialList->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleMaterialListIsDeniedForInactiveCollaborator() {
        /** @var MaterialList $materialList */
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
            ->request('GET', '/material_lists/'.$materialList->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleMaterialListIsAllowedForGuest() {
        /** @var MaterialList $materialList */
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->getUsername()])
            ->request('GET', '/material_lists/'.$materialList->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $materialList->getId(),
            'name' => $materialList->name,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
                'materialItems' => ['href' => '/material_items?materialList=%2Fmaterial_lists%2F'.$materialList->getId()],
            ],
        ]);
    }

    public function testGetSingleMaterialListIsAllowedForMember() {
        /** @var MaterialList $materialList */
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->getUsername()])
            ->request('GET', '/material_lists/'.$materialList->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $materialList->getId(),
            'name' => $materialList->name,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
                'materialItems' => ['href' => '/material_items?materialList=%2Fmaterial_lists%2F'.$materialList->getId()],
            ],
        ]);
    }

    public function testGetSingleMaterialListIsAllowedForManager() {
        /** @var MaterialList $materialList */
        $materialList = static::$fixtures['materialList1'];
        static::createClientWithCredentials()->request('GET', '/material_lists/'.$materialList->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $materialList->getId(),
            'name' => $materialList->name,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
                'materialItems' => ['href' => '/material_items?materialList=%2Fmaterial_lists%2F'.$materialList->getId()],
            ],
        ]);
    }

    public function testGetSingleMaterialListFromCampPrototypeIsAllowedForUnrelatedUser() {
        /** @var MaterialList $materialList */
        $materialList = static::$fixtures['materialList1campPrototype'];
        static::createClientWithCredentials()->request('GET', '/material_lists/'.$materialList->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $materialList->getId(),
            'name' => $materialList->name,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('campPrototype')],
                'materialItems' => ['href' => '/material_items?materialList=%2Fmaterial_lists%2F'.$materialList->getId()],
            ],
        ]);
    }
}
