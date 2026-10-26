<?php

namespace App\Tests\Api\HttpCache;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class AccessTagsTest extends ECampApiTestCase {
    public function testResponseWithCacheSecurityDelegateIsNotTaggedWithAccessRules() {
        $collaboration = static::getFixture('campCollaboration1manager');
        $activity = static::getFixture('activity1');

        $response = static::createClientWithCredentials()->request('GET', '/activities/'.$activity->getId());

        $this->assertResponseStatusCodeSame(200);
        $this->assertNotContains($collaboration->getId(), $this->getTags($response));
        $this->assertContains($activity->getId(), $this->getTags($response));
    }

    public function testResponseWithoutCacheSecurityDelegateIsTaggedWithAccessRules() {
        $collaboration = static::getFixture('campCollaboration1manager');
        $activity = static::getFixture('activity1');

        $response = static::createClientWithCredentials()->request('GET', '/activities/'.$activity->getId().'/comments');

        $this->assertResponseStatusCodeSame(200);
        $this->assertContains($collaboration->getId(), $this->getTags($response));
    }

    public function testCheckAccessIsTaggedWithCollaboration() {
        $collaboration = static::getFixture('campCollaboration1manager');

        $response = $this->checkAccess('camp1');

        $this->assertContains($collaboration->getId(), $this->getTags($response));
    }

    public function testCheckAccessToPublicCampIsTaggedWithCamp() {
        $camp = static::getFixture('campPrototype');

        $response = $this->checkAccess('campPrototype', ['email' => static::$fixtures['user4unrelated']->getEmail()]);

        $this->assertResponseStatusCodeSame(204);
        $this->assertContains($camp->getId(), $this->getTags($response));
    }

    public function testForbiddenCheckAccessIsNotTagged() {
        $response = $this->checkAccess('camp1', ['email' => static::$fixtures['user4unrelated']->getEmail()]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertSame([], $this->getTags($response));
    }

    private function checkAccess(string $campFixture, ?array $credentials = null) {
        return static::createClientWithCredentials($credentials)->request(
            'GET',
            '/authorization/check_access',
            ['query' => ['iri' => $this->getIriFor($campFixture)]]
        );
    }

    /**
     * @return string[]
     */
    private function getTags($response): array {
        return array_values(array_filter(explode(' ', $response->getHeaders(false)['xkey'][0] ?? '')));
    }
}
