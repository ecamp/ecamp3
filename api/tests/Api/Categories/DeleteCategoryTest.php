<?php

namespace App\Tests\Api\Categories;

use App\Entity\Category;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteCategoryTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testDeleteCategoryIsAllowedForCollaborator() {
        $category = static::$fixtures['category1'];
        static::createClientWithCredentials()->request('DELETE', '/categories/'.$category->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Category::class)->find($category->getId()));
    }
}
