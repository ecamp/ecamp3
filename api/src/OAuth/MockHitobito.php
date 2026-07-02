<?php

declare(strict_types=1);

namespace App\OAuth;

/**
 * Hitobito OAuth2 provider configured to use navikt/mock-oauth2-server for local development.
 *
 * Extends the real Hitobito provider unchanged so the full production code path
 * (HitobitoUser parsing, X-Scope header, /oauth/profile endpoint) is exercised.
 *
 * The only override is getBaseAuthorizationUrl(), which must use the browser-visible
 * externalBaseUrl so the user is redirected to the nginx-proxied mock server URL.
 * All internal API calls (token exchange, userinfo) go through a dedicated nginx
 * server block (port 3005) that translates Hitobito-style paths to the mock server's
 * OIDC paths:
 *
 *   /{issuer}/oauth/token   →  /{issuer}/token
 *   /{issuer}/oauth/profile →  /{issuer}/userinfo
 *
 * The X-Scope header added by the parent's fetchResourceOwnerDetails() is silently
 * ignored by navikt/mock-oauth2-server, so no override is needed.
 *
 * See reverse-proxy-nginx.conf (port 3005 server block).
 */
class MockHitobito extends Hitobito {
    /** External URL reachable by the browser (proxied through nginx at /mock-auth). */
    protected string $externalBaseUrl;

    /** baseUrl is inherited from Hitobito and points to the nginx hitobito adapter (port 3005). */
    public function getBaseAuthorizationUrl(): string {
        // The browser-visible authorize URL uses the Hitobito /oauth/authorize path;
        // nginx at port 3000 translates /mock-auth/{issuer}/oauth/authorize → /{issuer}/authorize.
        return $this->externalBaseUrl.'/oauth/authorize';
    }

    #[\Override]
    protected function getDefaultScopes(): array {
        // navikt/mock-oauth2-server requires openid in the scope (OIDC mandate).
        return ['openid', 'name'];
    }
}
