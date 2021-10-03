<?php

namespace App\Security\JWT;

use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\HttpFoundation\Request;

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
            $inputBag = $request->cookies->get($cookie, false);
            if ($inputBag) {
                $jwtCookies[] = $inputBag;
            }
        }

        if (count($this->cookies) !== count($jwtCookies)) {
            return false;
        }

        return implode('.', $jwtCookies);
    }
}
