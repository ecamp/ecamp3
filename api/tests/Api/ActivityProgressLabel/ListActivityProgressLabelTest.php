<?php

namespace App\Tests\Api\ActivityProgressLabel;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListActivityProgressLabelTest extends ECampApiTestCase {
    public function testListActivityProgressLabelsIsDeniedForAnonymousUser() {
        static::createBasicClient()
            ->request('GET', '/activity_progress_labels')
        ;
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListActivityProgressLabelsIsAllowedForLoggedInUserButFiltered() {
        // precondition: There is an activity progress label that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['activityProgressLabel1campUnrelated']);

        $response = static::createClientWithCredentials()
            ->request('GET', '/activity_progress_labels')
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 4,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('activityProgressLabel1')],
            ['href' => $this->getIriFor('activityProgressLabel2')],
            ['href' => $this->getIriFor('activityProgressLabel1Camp2')],
            ['href' => $this->getIriFor('activityProgressLabel2Camp2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListActivityProgressLabelsFilteredByCampIsAllowedForCollaborator() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials()
            ->request('GET', '/activity_progress_labels?camp=%2Fcamps%2F'.$camp->getId())
        ;
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
            ['href' => $this->getIriFor('activityProgressLabel1')],
            ['href' => $this->getIriFor('activityProgressLabel2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListActivityProgressLabelsFilteredByCampIsDeniedForUnrelatedUser() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/activity_progress_labels?camp=%2Fcamps%2F'.$camp->getId())
        ;

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }
}
