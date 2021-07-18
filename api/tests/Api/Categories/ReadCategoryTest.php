<?php

namespace App\Tests\Api\Categories;

use App\Entity\Category;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadCategoryTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testGetSingleCategoryIsAllowedForCollaborator() {
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
}
