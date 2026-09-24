<?php

namespace App\Tests\Api\Hitobito;

use App\Exception\HitobitoAccessTokenInvalidException;
use App\Service\Hitobito\AccessTokenProvider;
use App\Service\Hitobito\HitobitoProvider;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
class AccessTokenProviderTest extends TestCase {
    private AccessTokenProvider $accessTokenProvider;

    protected function setUp(): void {
        $this->accessTokenProvider = new AccessTokenProvider(
            'test_',
            base64_encode(random_bytes(SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_KEYBYTES)),
            true,
        );
    }

    public function testCookieCreationRetrieval() {
        $eCampUserId = 'a2f4f3879c85';
        $hitobitoUserId = 123;
        $token = 'hitobito-token';

        $accessToken = new AccessToken(['access_token' => $token]);
        $cookie = $this->accessTokenProvider->createCookie(HitobitoProvider::PBSMIDATA, $eCampUserId, $hitobitoUserId, $accessToken);
        $request = $this->requestWithCookie($cookie->getName(), $cookie->getValue());

        $authContext = $this->accessTokenProvider->getAccessToken($request, HitobitoProvider::PBSMIDATA, $eCampUserId);

        $this->assertSame($token, $authContext->getAccessToken());
        $this->assertSame($hitobitoUserId, $authContext->getUserId());
    }

    public function testCookieUserMismatch() {
        $accessToken = new AccessToken(['access_token' => 'hitobito-token']);
        $cookie = $this->accessTokenProvider->createCookie(HitobitoProvider::PBSMIDATA, 'a2f4f3879c85', 123, $accessToken);
        $request = $this->requestWithCookie($cookie->getName(), $cookie->getValue());

        $this->expectException(HitobitoAccessTokenInvalidException::class);

        $this->accessTokenProvider->getAccessToken($request, HitobitoProvider::PBSMIDATA, 'bae69a1c9fcc');
    }

    public function testMissingCookieRejected() {
        $request = new Request();

        $this->expectException(HitobitoAccessTokenInvalidException::class);

        $this->accessTokenProvider->getAccessToken($request, HitobitoProvider::PBSMIDATA, 'a2f4f3879c85');
    }

    private function requestWithCookie(string $name, string $value): Request {
        $request = new Request();
        $request->cookies->set($name, $value);

        return $request;
    }
}
