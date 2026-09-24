<?php

declare(strict_types=1);

namespace App\Service\Hitobito;

use App\Exception\HitobitoAccessTokenInvalidException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class AccessTokenProvider {
    public function __construct(
        private readonly string $cookiePrefix,
        private readonly string $tokenEncryptionKey,
        private readonly bool $cookieSecure,
    ) {}

    /**
     * createCookie encrypts the given Hitobito access token and returns the corresponding cookie that can be set in the API response.
     *
     * @param HitobitoProvider     $provider       Hitobito provider
     * @param string               $eCampUserId    ID of the eCamp user this token was issued to
     * @param int                  $hitobitoUserId User ID of the corresponding Hitobito user
     * @param AccessTokenInterface $accessToken    Access token
     *
     * @throws RandomException
     * @throws \SodiumException
     */
    public function createCookie(HitobitoProvider $provider, string $eCampUserId, int $hitobitoUserId, AccessTokenInterface $accessToken): Cookie {
        $payload = [
            'user_id' => $hitobitoUserId,
            'access_token' => $accessToken->getToken(),
        ];

        $encoded = json_encode($payload);

        $nonce = random_bytes(SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES);
        $cipherText = sodium_crypto_aead_chacha20poly1305_ietf_encrypt($encoded, $eCampUserId, $nonce, $this->getKey());
        $expires = $accessToken->getExpires() ?? (time() + 3600);

        return Cookie::create(self::getCookieName($this->cookiePrefix, $provider))
            ->withValue(base64_encode($nonce.$cipherText))
            ->withHttpOnly()
            ->withSameSite('strict')
            ->withSecure($this->cookieSecure)
            ->withExpires($expires)
        ;
    }

    /**
     * getAccessToken reads the given request, decrypts the access token and returns it, along with the hitobito user id.
     *
     * @param Request          $request     Reference to current request
     * @param HitobitoProvider $provider    Used HitobitoProvider
     * @param string           $eCampUserId ID of the current eCamp user
     *
     * @return AuthContext Access token and Hitobito user id
     */
    public function getAccessToken(Request $request, HitobitoProvider $provider, string $eCampUserId): AuthContext {
        $cookieValue = $request->cookies->get(self::getCookieName($this->cookiePrefix, $provider));
        if (null === $cookieValue) {
            throw new HitobitoAccessTokenInvalidException('No Hitobito access token cookie present');
        }

        $decoded = base64_decode($cookieValue, true);
        if (false === $decoded || \strlen($decoded) <= SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES) {
            throw new HitobitoAccessTokenInvalidException('Malformed Hitobito access token cookie');
        }

        $nonce = substr($decoded, 0, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES);
        $cipherText = substr($decoded, SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES);

        try {
            $payload = sodium_crypto_aead_chacha20poly1305_ietf_decrypt($cipherText, $eCampUserId, $nonce, $this->getKey());
        } catch (\SodiumException) {
            throw new HitobitoAccessTokenInvalidException('Could not decrypt Hitobito access token');
        }

        if (false === $payload) {
            throw new HitobitoAccessTokenInvalidException('Could not decrypt Hitobito access token');
        }

        $decoded = json_decode($payload, true);
        if (!is_array($decoded) || !isset($decoded['user_id']) || !isset($decoded['access_token'])) {
            throw new HitobitoAccessTokenInvalidException('Invalid access token payload');
        }

        return new AuthContext(
            $decoded['user_id'],
            $decoded['access_token'],
        );
    }

    private function getCookieName(string $cookiePrefix, HitobitoProvider $provider): string {
        return "{$cookiePrefix}hitobito_{$provider->value}_token";
    }

    private function getKey(): string {
        $key = base64_decode($this->tokenEncryptionKey, true);

        if (false === $key || SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_KEYBYTES !== \strlen($key)) {
            throw new \RuntimeException('Token Encryption Key must be a base64 encoded 256 bit (32 byte) key');
        }

        return $key;
    }
}
