<?php

namespace App\Security\JWT;

use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Applies the fix from https://github.com/lexik/LexikJWTAuthenticationBundle/pull/931 early for us.
 * This allows us to delete just the accessible of the two cookies via JavaScript when logging out.
 */
class SplitCookieExtractor implements TokenExtractorInterface {
    /**
     * @var array
     */
    private $cookies;

    /**
     * @param array $cookies
     */
    public function __construct($cookies) {
        $this->cookies = $cookies;
    }

    public function extract(Request $request): bool|string {
        $jwtCookies = [];

        foreach ($this->cookies as $cookie) {
            $jwtCookies[] = $request->cookies->get($cookie, false);
        }

        if (count($this->cookies) !== count(array_filter($jwtCookies))) {
            return false;
        }

        return implode('.', $jwtCookies);
    }
}
