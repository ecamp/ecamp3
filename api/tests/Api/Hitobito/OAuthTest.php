<?php

namespace App\Tests\Api\Hitobito;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class OAuthTest extends ECampApiTestCase {
    public function testStartReturns404ForUnknownProvider() {
        $client = static::createClientWithCredentials();
        $client->request('GET', '/hitobito/unknownprovider/oauth?callback='.urlencode('/camps/hitobito/pbsmidata/import'));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testStartReturns400ForInvalidCallback() {
        $client = static::createClientWithCredentials();
        $client->request('GET', '/hitobito/pbsmidata/oauth?callback='.urlencode('/not/allowed/path'));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testCallbackDoesNotRequireAuthentication() {
        $client = static::createBasicClient();
        $client->request('GET', '/hitobito/pbsmidata/oauth/callback');

        // Upon failure, user is redirected back to frontend
        $this->assertResponseStatusCodeSame(302);
    }

    public function testAuthorizedStartRedirectsToHitobito() {
        $client = static::createClientWithCredentials();
        $client->request('GET', '/hitobito/pbsmidata/oauth?callback='.urlencode('/camps/hitobito/pbsmidata/import'));

        $this->assertResponseStatusCodeSame(302);
    }
}
