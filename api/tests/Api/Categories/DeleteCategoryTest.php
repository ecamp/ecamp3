<?php

namespace App\Tests\Api\Categories;

use App\Entity\Category;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteCategoryTest extends ECampApiTestCase {
    public function testDeleteCategoryIsDeniedForAnonymousUser() {
        $category = static::getFixture('categoryWithNoActivities');
        static::createBasicClient()->request('DELETE', '/categories/'.$category->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteCategoryIsDeniedForUnrelatedUser() {
        $category = static::getFixture('categoryWithNoActivities');
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
        $category = static::getFixture('categoryWithNoActivities');
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
        $category = static::getFixture('categoryWithNoActivities');
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
        $category = static::getFixture('categoryWithNoActivities');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('DELETE', '/categories/'.$category->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Category::class)->find($category->getId()));
    }

    public function testDeleteCategoryIsAllowedForManager() {
        $category = static::getFixture('categoryWithNoActivities');
        static::createClientWithCredentials()->request('DELETE', '/categories/'.$category->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Category::class)->find($category->getId()));
    }

    public function testDeleteCategoryFromCampPrototypeIsDeniedForUnrelatedUser() {
        $category = static::getFixture('category1campPrototype');
        static::createClientWithCredentials()->request('DELETE', '/categories/'.$category->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteCategoryValidatesThatCategoryHasNoActivities() {
        $category = static::getFixture('category1');
        static::createClientWithCredentials()
            ->request('DELETE', '/categories/'.$category->getId())
        ;

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'activities: It\'s not possible to delete a category as long as it has an activity linked to it.',
        ]);
    }

    public function testDeleteCategoryPurgesCacheTags() {
        $client = static::createClientWithCredentials();
        $cacheManager = $this->mockCacheManager();

        $category = static::getFixture('categoryWithNoActivities');
        $client->request('DELETE', '/categories/'.$category->getId());

        $this->assertResponseStatusCodeSame(204);

        $camp = $category->getCamp();
        $rootContentNode = $category->getRootContentNode();
        self::assertEqualsCanonicalizing([
            $category->getId(),
            '/categories',
            '/camps/'.$camp->getId().'/categories',
            $camp->getId().'#categories',
            '/content_nodes',
            '/content_node/column_layouts',
            $rootContentNode->getId(),
            $rootContentNode->getId().'#rootDescendants',
        ], $cacheManager->getInvalidatedTags());
    }
}
