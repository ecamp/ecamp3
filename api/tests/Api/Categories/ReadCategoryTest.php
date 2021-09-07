<?php

namespace App\Tests\Api\Categories;

use App\Entity\Category;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadCategoryTest extends ECampApiTestCase {
    public function testGetSingleCategoryIsDeniedForAnonymousUser() {
        /** @var Category $category */
        $category = static::$fixtures['category1'];
        static::createClient()->request('GET', '/categories/'.$category->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleCategoryIsDeniedForUnrelatedUser() {
        /** @var Category $category */
        $category = static::$fixtures['category1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('GET', '/categories/'.$category->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleCategoryIsDeniedForInactiveCollaborator() {
        /** @var Category $category */
        $category = static::$fixtures['category1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('GET', '/categories/'.$category->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleCategoryIsAllowedForGuest() {
        /** @var Category $category */
        $category = static::$fixtures['category1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
            ->request('GET', '/categories/'.$category->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $category->getId(),
            'short' => $category->short,
            'name' => $category->name,
            'color' => $category->color,
            'numberingStyle' => $category->numberingStyle,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
                'rootContentNode' => ['href' => $this->getIriFor('contentNode2')],
                'preferredContentTypes' => [],
                //'contentNodes' => ['href' => '/content_nodes?owner=/categories/'.$category->getId()],
            ],
        ]);
    }

    public function testGetSingleCategoryIsAllowedForMember() {
        /** @var Category $category */
        $category = static::$fixtures['category1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
            ->request('GET', '/categories/'.$category->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $category->getId(),
            'short' => $category->short,
            'name' => $category->name,
            'color' => $category->color,
            'numberingStyle' => $category->numberingStyle,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
                'rootContentNode' => ['href' => $this->getIriFor('contentNode2')],
                'preferredContentTypes' => [],
                //'contentNodes' => ['href' => '/content_nodes?owner=/categories/'.$category->getId()],
            ],
        ]);
    }

    public function testGetSingleCategoryIsAllowedForManager() {
        /** @var Category $category */
        $category = static::$fixtures['category1'];
        static::createClientWithCredentials()->request('GET', '/categories/'.$category->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $category->getId(),
            'short' => $category->short,
            'name' => $category->name,
            'color' => $category->color,
            'numberingStyle' => $category->numberingStyle,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
                'rootContentNode' => ['href' => $this->getIriFor('contentNode2')],
                'preferredContentTypes' => [],
                //'contentNodes' => ['href' => '/content_nodes?owner=/categories/'.$category->getId()],
            ],
        ]);
    }

    public function testGetSingleCategoryFromCampPrototypeIsAllowedForUnrelatedUser() {
        /** @var Category $category */
        $category = static::$fixtures['category1campPrototype'];
        static::createClientWithCredentials()->request('GET', '/categories/'.$category->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $category->getId(),
            'short' => $category->short,
            'name' => $category->name,
            'color' => $category->color,
            'numberingStyle' => $category->numberingStyle,
            '_links' => [
                'camp' => ['href' => $this->getIriFor('campPrototype')],
                'rootContentNode' => ['href' => $this->getIriFor('contentNode2campPrototype')],
                //'contentNodes' => ['href' => '/content_nodes?owner=/categories/'.$category->getId()],
            ],
        ]);
    }
}
