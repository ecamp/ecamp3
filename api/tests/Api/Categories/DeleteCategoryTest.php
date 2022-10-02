<?php

namespace App\Tests\Api\Categories;

use App\Entity\Category;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteCategoryTest extends ECampApiTestCase {
    public function testDeleteCategoryIsDeniedForAnonymousUser() {
        $category = static::$fixtures['categoryWithNoActivities'];
        static::createBasicClient()->request('DELETE', '/categories/'.$category->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteCategoryIsDeniedForUnrelatedUser() {
        $category = static::$fixtures['categoryWithNoActivities'];
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('DELETE', '/categories/'.$category->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteCategoryIsDeniedForInactiveCollaborator() {
        $category = static::$fixtures['categoryWithNoActivities'];
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('DELETE', '/categories/'.$category->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteCategoryIsDeniedForGuest() {
        $category = static::$fixtures['categoryWithNoActivities'];
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('DELETE', '/categories/'.$category->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteCategoryIsAllowedForMember() {
        $category = static::$fixtures['categoryWithNoActivities'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('DELETE', '/categories/'.$category->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Category::class)->find($category->getId()));
    }

    public function testDeleteCategoryIsAllowedForManager() {
        $category = static::$fixtures['categoryWithNoActivities'];
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

    public function testDeleteCategoryValidatesThatCategoryHasNoActivities() {
        $category = static::$fixtures['category1'];
        static::createClientWithCredentials()
            ->request('DELETE', '/categories/'.$category->getId())
        ;

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'activities: It\'s not possible to delete a category as long as it has an activity linked to it.',
        ]);
    }
}
