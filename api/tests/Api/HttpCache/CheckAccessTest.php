<?php

namespace App\Tests\Api\HttpCache;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CheckAccessTest extends ECampApiTestCase {
    public function testRequiresLogin() {
        static::createBasicClient()->request('GET', '/authorization/check_access', ['query' => ['iri' => $this->getIriFor('camp1')]]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testIsGrantedForCollaboratorOfCamp() {
        $response = $this->checkAccess($this->getIriFor('camp1'));

        $this->assertResponseStatusCodeSame(204);
        $this->assertStringContainsString('public', $response->getHeaders()['cache-control'][0]);
        $this->assertMatchesRegularExpression('/s-maxage=(\d+)/', $response->getHeaders()['cache-control'][0]);
    }

    public function testGrantDoesNotOutliveTheToken() {
        $response = $this->checkAccess($this->getIriFor('camp1'));

        preg_match('/s-maxage=(\d+)/', $response->getHeaders()['cache-control'][0], $matches);
        $this->assertLessThanOrEqual(43200, (int) $matches[1]);
        $this->assertGreaterThan(0, (int) $matches[1]);
    }

    public function testIsGrantedForPublicCampToUnrelatedUser() {
        $this->checkAccess($this->getIriFor('campPrototype'), ['email' => static::$fixtures['user4unrelated']->getEmail()]);

        $this->assertResponseStatusCodeSame(204);
    }

    public function testIsForbiddenForUnrelatedUserAndNotCacheable() {
        $response = $this->checkAccess($this->getIriFor('camp1'), ['email' => static::$fixtures['user4unrelated']->getEmail()]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertStringContainsString('no-store', $response->getHeaders(false)['cache-control'][0]);
        $this->assertStringNotContainsString('public', $response->getHeaders(false)['cache-control'][0]);
    }

    public function testIsForbiddenForInactiveCollaborator() {
        $this->checkAccess($this->getIriFor('camp1'), ['email' => static::$fixtures['user5inactive']->getEmail()]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testIsForbiddenForIriThatIsNoResource() {
        $this->checkAccess('public');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testIsForbiddenForResourceThatDoesNotExist() {
        $this->checkAccess('/camps/000000000000');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testIsBadRequestWithoutIri() {
        static::createClientWithCredentials()->request('GET', '/authorization/check_access');

        $this->assertResponseStatusCodeSame(400);
    }

    private function checkAccess(string $iri, ?array $credentials = null) {
        return static::createClientWithCredentials($credentials)->request('GET', '/authorization/check_access', ['query' => ['iri' => $iri]]);
    }
}
