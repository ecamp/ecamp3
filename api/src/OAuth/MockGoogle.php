<?php

declare(strict_types=1);

namespace App\OAuth;

use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Token\AccessToken;

/**
 * Google OAuth2 provider configured to use navikt/mock-oauth2-server for local development.
 *
 * Extends the real Google provider so the production code path (GoogleUser, assertMatchingDomain,
 * scope handling) is exercised unchanged. Only the three hard-coded Google URLs are replaced with
 * configurable ones pointing at the mock server.
 */
class MockGoogle extends Google {
    /** Internal Docker network URL (API → mock-oauth2 container), includes issuer path. */
    protected string $baseUrl;

    /** External URL reachable by the browser (proxied through nginx at /mock-auth). */
    protected string $externalBaseUrl;

    public function getBaseAuthorizationUrl(): string {
        return $this->externalBaseUrl.'/authorize';
    }

    public function getBaseAccessTokenUrl(array $params): string {
        return $this->baseUrl.'/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string {
        return $this->baseUrl.'/userinfo';
    }
}
