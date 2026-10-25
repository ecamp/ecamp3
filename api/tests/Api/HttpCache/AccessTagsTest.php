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

    public function testCacheAccessIsTaggedWithCollaboration() {
        $collaboration = static::getFixture('campCollaboration1manager');

        $response = $this->requestCacheAccess('camp1');

        $this->assertContains($collaboration->getId(), $this->getTags($response));
    }

    public function testCacheAccessToPublicCampIsTaggedWithCamp() {
        $camp = static::getFixture('campPrototype');

        $response = $this->requestCacheAccess('campPrototype', ['email' => static::$fixtures['user4unrelated']->getEmail()]);

        $this->assertSame(['granted'], $response->getHeaders()['x-cache-access']);
        $this->assertContains($camp->getId(), $this->getTags($response));
    }

    public function testDeniedCacheAccessIsNotTagged() {
        $response = $this->requestCacheAccess('camp1', ['email' => static::$fixtures['user4unrelated']->getEmail()]);

        $this->assertSame(['denied'], $response->getHeaders()['x-cache-access']);
        $this->assertSame([], $this->getTags($response));
    }

    private function requestCacheAccess(string $campFixture, ?array $credentials = null) {
        return static::createClientWithCredentials($credentials)->request(
            'GET',
            '/_cache_access',
            ['headers' => ['X-Cache-Security-Delegate' => $this->getIriFor($campFixture)]]
        );
    }

    /**
     * @return string[]
     */
    private function getTags($response): array {
        return array_values(array_filter(explode(' ', $response->getHeaders(false)['xkey'][0] ?? '')));
    }
}
