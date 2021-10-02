<?php

namespace App\Tests\Api\ContentNodes\SingleText;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListSingleTextTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testListSingleTextsIsAllowedForCollaborator() {
        $response = static::createClientWithCredentials()->request('GET', '/content_node/single_texts');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 2,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('singleText1')],
            ['href' => $this->getIriFor('singleText2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListSingleTextsFilteredByParentIsAllowedForCollaborator() {
        $parent = static::$fixtures['columnLayout1'];
        $response = static::createClientWithCredentials()->request('GET', '/content_node/single_texts?parent='.$this->getIriFor('columnLayout1'));
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 1,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('singleText1')],
        ], $response->toArray()['_links']['items']);
    }
}
