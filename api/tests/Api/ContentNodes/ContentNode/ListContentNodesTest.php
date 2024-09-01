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

    public function testListContentNodesIsAllowedForLoggedInUserButFiltered() {
        $response = static::createClientWithCredentials()->request('GET', '/content_nodes');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 23,
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
            ['href' => $this->getIriFor('columnLayout2')],
            ['href' => $this->getIriFor('columnLayoutChild1')],
            ['href' => $this->getIriFor('columnLayout2Child1')],
            ['href' => $this->getIriFor('columnLayout3')],
            ['href' => $this->getIriFor('checklistNode3')],
            ['href' => $this->getIriFor('columnLayout4')],
            ['href' => $this->getIriFor('columnLayout5')],
            ['href' => $this->getIriFor('columnLayout1camp2')],
            ['href' => $this->getIriFor('columnLayout2camp2')],
            ['href' => $this->getIriFor('columnLayout1campPrototype')],
            ['href' => $this->getIriFor('columnLayout2campPrototype')],
            ['href' => $this->getIriFor('singleText1')],
            ['href' => $this->getIriFor('singleText2')],
            ['href' => $this->getIriFor('safetyConsiderations1')],
            ['href' => $this->getIriFor('materialNode1')],
            ['href' => $this->getIriFor('materialNode2')],
            ['href' => $this->getIriFor('storyboard1')],
            ['href' => $this->getIriFor('storyboard2')],
            ['href' => $this->getIriFor('multiSelect1')],
            ['href' => $this->getIriFor('multiSelect2')],
            ['href' => $this->getIriFor('responsiveLayout1')],
        ], $response->toArray()['_links']['items']);
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
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
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
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/content_nodes?period=%2Fperiods%2F'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(400);

        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('period1').'".',
        ]);
    }

    public function testListContentNodesFilteredByPeriodInCampPrototypeIsAllowedForCollaborator() {
        $period = static::getFixture('period1campPrototype');
        $response = static::createClientWithCredentials()->request('GET', '/content_nodes?period=%2Fperiods%2F'.$period->getId());
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
            ['href' => $this->getIriFor('columnLayout1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }
}
