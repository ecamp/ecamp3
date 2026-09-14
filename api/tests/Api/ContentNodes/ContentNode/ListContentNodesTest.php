<?php

namespace App\Tests\Api\ContentNodes\ContentNode;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListContentNodesTest extends ECampApiTestCase {
    // TODO add tests for filtering by contentType and root

    public function testListContentNodesIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/content_nodes');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListContentNodesWithoutFilterIsNotAllowedForLoggedInUser() {
        static::createClientWithCredentials()->request('GET', '/content_nodes');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testListContentNodesFilteredByPeriodIsAllowedForCollaborator() {
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials()->request('GET', '/content_nodes?period=%2Fperiods%2F'.$period->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 14,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('columnLayout1')],
            ['href' => $this->getIriFor('checklistNode1')],
            ['href' => $this->getIriFor('columnLayoutChild1')],
            ['href' => $this->getIriFor('columnLayout3')],
            ['href' => $this->getIriFor('checklistNode3')],
            ['href' => $this->getIriFor('singleText1')],
            ['href' => $this->getIriFor('singleText2')],
            ['href' => $this->getIriFor('safetyConsiderations1')],
            ['href' => $this->getIriFor('materialNode1')],
            ['href' => $this->getIriFor('storyboard1')],
            ['href' => $this->getIriFor('storyboard2')],
            ['href' => $this->getIriFor('multiSelect1')],
            ['href' => $this->getIriFor('multiSelect2')],
            ['href' => $this->getIriFor('responsiveLayout1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListContentNodesFilteredByPeriodIsDeniedForUnrelatedUser() {
        $period = static::getFixture('period1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/content_nodes?period=%2Fperiods%2F'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(400);

        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('period1').'".',
        ]);
    }

    public function testListContentNodesFilteredByPeriodIsDeniedForInactiveCollaborator() {
        $period = static::getFixture('period1');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/content_nodes?period=%2Fperiods%2F'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(400);

        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('period1').'".',
        ]);
    }

    public function testListContentNodesFilteredByPeriodInCampPrototypeIsAllowedForUnrelatedUser() {
        $period = static::getFixture('period1campPrototype');
        $response = static::createClientWithCredentials()->request('GET', '/content_nodes?period=%2Fperiods%2F'.$period->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 8,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('checklistNodeCampPrototype')],
            ['href' => $this->getIriFor('columnLayout1campPrototype')],
            ['href' => $this->getIriFor('columnLayout3campPrototype')],
            ['href' => $this->getIriFor('materialNodeCampPrototype')],
            ['href' => $this->getIriFor('multiSelectCampPrototype')],
            ['href' => $this->getIriFor('responsiveLayoutCampPrototype')],
            ['href' => $this->getIriFor('singleTextCampPrototype')],
            ['href' => $this->getIriFor('storyboardCampPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListContentNodesFilteredByPeriodInSharedCampIsAllowedForUnrelatedUser() {
        $period = static::getFixture('period1campShared');
        $response = static::createClientWithCredentials()->request('GET', '/content_nodes?period=%2Fperiods%2F'.$period->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 8,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('checklistNodeCampShared')],
            ['href' => $this->getIriFor('columnLayout1campShared')],
            ['href' => $this->getIriFor('columnLayout3campShared')],
            ['href' => $this->getIriFor('materialNodeCampShared')],
            ['href' => $this->getIriFor('multiSelectCampShared')],
            ['href' => $this->getIriFor('responsiveLayoutCampShared')],
            ['href' => $this->getIriFor('singleTextCampShared')],
            ['href' => $this->getIriFor('storyboardCampShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListContentNodesFilteredByPeriodInSharedCampIsAllowedForInactiveUser() {
        $period = static::getFixture('period1campShared');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/content_nodes?period=%2Fperiods%2F'.$period->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 8,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('checklistNodeCampShared')],
            ['href' => $this->getIriFor('columnLayout1campShared')],
            ['href' => $this->getIriFor('columnLayout3campShared')],
            ['href' => $this->getIriFor('materialNodeCampShared')],
            ['href' => $this->getIriFor('multiSelectCampShared')],
            ['href' => $this->getIriFor('responsiveLayoutCampShared')],
            ['href' => $this->getIriFor('singleTextCampShared')],
            ['href' => $this->getIriFor('storyboardCampShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListContentNodesFilteredByPeriodInSharedCampIsAllowedForInvitedUser() {
        $period = static::getFixture('period1campShared');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])
            ->request('GET', '/content_nodes?period=%2Fperiods%2F'.$period->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 8,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('checklistNodeCampShared')],
            ['href' => $this->getIriFor('columnLayout1campShared')],
            ['href' => $this->getIriFor('columnLayout3campShared')],
            ['href' => $this->getIriFor('materialNodeCampShared')],
            ['href' => $this->getIriFor('multiSelectCampShared')],
            ['href' => $this->getIriFor('responsiveLayoutCampShared')],
            ['href' => $this->getIriFor('singleTextCampShared')],
            ['href' => $this->getIriFor('storyboardCampShared')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListRootContentNodesFilteredByPeriodIsAllowedForCollaborator() {
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials()->request('GET', '/content_nodes?isRoot=true&period=%2Fperiods%2F'.$period->getId());
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
            ['href' => $this->getIriFor('columnLayout1')],
            ['href' => $this->getIriFor('columnLayout3')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListNonRootContentNodesFilteredByPeriodIsAllowedForCollaborator() {
        $period = static::getFixture('period1');
        $response = static::createClientWithCredentials()->request('GET', '/content_nodes?isRoot=false&period=%2Fperiods%2F'.$period->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 12,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('checklistNode1')],
            ['href' => $this->getIriFor('columnLayoutChild1')],
            ['href' => $this->getIriFor('checklistNode3')],
            ['href' => $this->getIriFor('singleText1')],
            ['href' => $this->getIriFor('singleText2')],
            ['href' => $this->getIriFor('safetyConsiderations1')],
            ['href' => $this->getIriFor('materialNode1')],
            ['href' => $this->getIriFor('storyboard1')],
            ['href' => $this->getIriFor('storyboard2')],
            ['href' => $this->getIriFor('multiSelect1')],
            ['href' => $this->getIriFor('multiSelect2')],
            ['href' => $this->getIriFor('responsiveLayout1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListContentNodesFilteredByRootIsAllowedForCollaborator() {
        $root = static::getFixture('columnLayout1');
        $response = static::createClientWithCredentials()
            ->request('GET', '/content_nodes?root=%2Fcontent_nodes%2F'.$root->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 12]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('columnLayout1')],
            ['href' => $this->getIriFor('checklistNode1')],
            ['href' => $this->getIriFor('columnLayoutChild1')],
            ['href' => $this->getIriFor('responsiveLayout1')],
            ['href' => $this->getIriFor('storyboard1')],
            ['href' => $this->getIriFor('storyboard2')],
            ['href' => $this->getIriFor('singleText1')],
            ['href' => $this->getIriFor('singleText2')],
            ['href' => $this->getIriFor('multiSelect1')],
            ['href' => $this->getIriFor('multiSelect2')],
            ['href' => $this->getIriFor('materialNode1')],
            ['href' => $this->getIriFor('safetyConsiderations1')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListContentNodesFilteredByRootIsDeniedForUnrelatedUser() {
        $root = static::getFixture('columnLayout1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/content_nodes?root=%2Fcontent_nodes%2F'.$root->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListContentNodesFilteredByCampIsAllowedForCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials()
            ->request('GET', '/content_nodes?camp=%2Fcamps%2F'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 14]);
    }

    public function testListContentNodesFilteredByCampIsDeniedForUnrelatedUser() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/content_nodes?camp=%2Fcamps%2F'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(400);

        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }
}
