<?php

namespace App\Tests\Api\Categories;

use App\Entity\Category;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteCategoryTest extends ECampApiTestCase {
    public function testDeleteCategoryIsDeniedForAnonymousUser() {
        $category = static::$fixtures['category1'];
        static::createBasicClient()->request('DELETE', '/categories/'.$category->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteCategoryIsDeniedForUnrelatedUser() {
        $category = static::$fixtures['category1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('DELETE', '/categories/'.$category->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteCategoryIsDeniedForInactiveCollaborator() {
        $category = static::$fixtures['category1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('DELETE', '/categories/'.$category->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteCategoryIsDeniedForGuest() {
        $category = static::$fixtures['category1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
            ->request('DELETE', '/categories/'.$category->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteCategoryIsAllowedForMember() {
        $category = static::$fixtures['category1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
            ->request('DELETE', '/categories/'.$category->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Category::class)->find($category->getId()));
    }

    public function testDeleteCategoryIsAllowedForManager() {
        $category = static::$fixtures['category1'];
        static::createClientWithCredentials()->request('DELETE', '/categories/'.$category->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Category::class)->find($category->getId()));
    }

    public function testDeleteCategoryFromCampPrototypeIsDeniedForUnrelatedUser() {
        $category = static::$fixtures['category1campPrototype'];
        static::createClientWithCredentials()->request('DELETE', '/categories/'.$category->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }
}
