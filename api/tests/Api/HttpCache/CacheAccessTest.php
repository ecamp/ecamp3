<?php

namespace App\Tests\Api\HttpCache;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CacheAccessTest extends ECampApiTestCase {
    public function testIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/_cache_access', ['headers' => ['X-Cache-Security-Delegate' => $this->getIriFor('camp1')]]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testIsGrantedForCollaboratorOfCamp() {
        $response = $this->requestAccess('camp1');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame(['granted'], $response->getHeaders()['x-cache-access']);
        $this->assertStringContainsString('public', $response->getHeaders()['cache-control'][0]);
        $this->assertMatchesRegularExpression('/s-maxage=(\d+)/', $response->getHeaders()['cache-control'][0]);
    }

    public function testGrantDoesNotOutliveTheToken() {
        $response = $this->requestAccess('camp1');

        preg_match('/s-maxage=(\d+)/', $response->getHeaders()['cache-control'][0], $matches);
        $this->assertLessThanOrEqual(43200, (int) $matches[1]);
        $this->assertGreaterThan(0, (int) $matches[1]);
    }

    public function testIsGrantedForPublicCampToUnrelatedUser() {
        $response = $this->requestAccess('campPrototype', ['email' => static::$fixtures['user4unrelated']->getEmail()]);

        $this->assertSame(['granted'], $response->getHeaders()['x-cache-access']);
    }

    public function testIsDeniedForUnrelatedUserAndNotCacheable() {
        $response = $this->requestAccess('camp1', ['email' => static::$fixtures['user4unrelated']->getEmail()]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame(['denied'], $response->getHeaders()['x-cache-access']);
        $this->assertStringContainsString('no-store', $response->getHeaders()['cache-control'][0]);
        $this->assertStringNotContainsString('public', $response->getHeaders()['cache-control'][0]);
    }

    public function testIsDeniedForInactiveCollaborator() {
        $response = $this->requestAccess('camp1', ['email' => static::$fixtures['user5inactive']->getEmail()]);

        $this->assertSame(['denied'], $response->getHeaders()['x-cache-access']);
    }

    public function testIsDeniedWithoutDelegate() {
        $response = static::createClientWithCredentials()->request('GET', '/_cache_access');

        $this->assertSame(['denied'], $response->getHeaders()['x-cache-access']);
    }

    public function testIsDeniedForDelegateThatIsNoResource() {
        $response = static::createClientWithCredentials()->request('GET', '/_cache_access', ['headers' => ['X-Cache-Security-Delegate' => 'public']]);

        $this->assertSame(['denied'], $response->getHeaders()['x-cache-access']);
    }

    private function requestAccess(string $campFixture, ?array $credentials = null) {
        return static::createClientWithCredentials($credentials)->request(
            'GET',
            '/_cache_access',
            ['headers' => ['X-Cache-Security-Delegate' => $this->getIriFor($campFixture)]]
        );
    }
}
