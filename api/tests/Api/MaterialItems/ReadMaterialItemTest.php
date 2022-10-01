<?php

namespace App\Tests\Api\MaterialItems;

use App\Entity\MaterialItem;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadMaterialItemTest extends ECampApiTestCase {
    public function testGetSingleMaterialItemIsDeniedForAnonymousUser() {
        /** @var MaterialItem $materialItem */
        $materialItem = static::$fixtures['materialItem1'];
        static::createBasicClient()->request('GET', '/material_items/'.$materialItem->getId())
        ;
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleMaterialItemIsDeniedForUnrelatedUser() {
        /** @var MaterialItem $materialItem */
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/material_items/'.$materialItem->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleMaterialItemIsDeniedForInactiveCollaborator() {
        /** @var MaterialItem $materialItem */
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/material_items/'.$materialItem->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleMaterialItemIsAllowedForGuest() {
        /** @var MaterialItem $materialItem */
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('GET', '/material_items/'.$materialItem->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $materialItem->getId(),
            'quantity' => (int) $materialItem->quantity,
            'unit' => $materialItem->unit,
            'article' => $materialItem->article,
            '_links' => [
                'period' => null,
                'materialList' => ['href' => $this->getIriFor('materialList1')],
                'materialNode' => ['href' => $this->getIriFor('materialNode1')],
            ],
        ]);
    }

    public function testGetSingleMaterialItemIsAllowedForMember() {
        /** @var MaterialItem $materialItem */
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('GET', '/material_items/'.$materialItem->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $materialItem->getId(),
            'quantity' => (int) $materialItem->quantity,
            'unit' => $materialItem->unit,
            'article' => $materialItem->article,
            '_links' => [
                'period' => null,
                'materialList' => ['href' => $this->getIriFor('materialList1')],
                'materialNode' => ['href' => $this->getIriFor('materialNode1')],
            ],
        ]);
    }

    public function testGetSingleMaterialItemIsAllowedForManager() {
        /** @var MaterialItem $materialItem */
        $materialItem = static::$fixtures['materialItem1'];
        static::createClientWithCredentials()->request('GET', '/material_items/'.$materialItem->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $materialItem->getId(),
            'quantity' => (int) $materialItem->quantity,
            'unit' => $materialItem->unit,
            'article' => $materialItem->article,
            '_links' => [
                'period' => null,
                'materialList' => ['href' => $this->getIriFor('materialList1')],
                'materialNode' => ['href' => $this->getIriFor('materialNode1')],
            ],
        ]);
    }

    public function testGetSingleMaterialItemInCampPrototypeIsAllowedForUnrelatedUser() {
        /** @var MaterialItem $materialItem */
        $materialItem = static::$fixtures['materialItem1period1campPrototype'];
        static::createClientWithCredentials()->request('GET', '/material_items/'.$materialItem->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $materialItem->getId(),
            'quantity' => (int) $materialItem->quantity,
            'unit' => $materialItem->unit,
            'article' => $materialItem->article,
            '_links' => [
                'period' => ['href' => $this->getIriFor('period1campPrototype')],
                'materialList' => ['href' => $this->getIriFor('materialList1campPrototype')],
                'materialNode' => null,
            ],
        ]);
    }
}
