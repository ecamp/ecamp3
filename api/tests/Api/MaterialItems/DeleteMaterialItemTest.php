<?php

namespace App\Tests\Api\MaterialItems;

use App\Entity\MaterialItem;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteMaterialItemTest extends ECampApiTestCase {
    public function testDeleteMaterialItemIsDeniedForAnonymousUser() {
        $materialItem = static::getFixture('materialItem1');
        static::createBasicClient()->request('DELETE', '/material_items/'.$materialItem->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteMaterialItemIsDeniedForUnrelatedUser() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('DELETE', '/material_items/'.$materialItem->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteMaterialItemIsDeniedForInactiveCollaborator() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('DELETE', '/material_items/'.$materialItem->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteMaterialItemIsDeniedForGuest() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('DELETE', '/material_items/'.$materialItem->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteMaterialItemIsAllowedForMember() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('DELETE', '/material_items/'.$materialItem->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(MaterialItem::class)->find($materialItem->getId()));
    }

    public function testDeleteMaterialItemIsAllowedForManager() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('DELETE', '/material_items/'.$materialItem->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(MaterialItem::class)->find($materialItem->getId()));
    }

    public function testDeleteMaterialItemFromCampPrototypeIsDeniedForUnrelatedUser() {
        $materialItem = static::getFixture('materialItem1period1campPrototype');
        static::createClientWithCredentials()->request('DELETE', '/material_items/'.$materialItem->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }
}
