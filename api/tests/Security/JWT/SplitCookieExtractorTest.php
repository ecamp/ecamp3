<?php

namespace App\Tests\Security\JWT;

use App\Security\JWT\SplitCookieExtractor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
class SplitCookieExtractorTest extends TestCase {
    public const HEADER = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXQSJ9';
    public const DATA = 'eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ';
    public const SIGNATURE = 'peuvjFPw8tuUhdkmCRV-a1qVB7yYQE2a0dFuBcrv0DM';

    public const JWT_HP = self::HEADER.'.'.self::DATA;
    public const JWT_S = self::SIGNATURE;
    public const JWT_HP_COOKIE_KEY = 'jwt_hp';
    public const JWT_S_COOKIE_KEY = 'jwt_s';

    private SplitCookieExtractor $validatingSplitCookieExtractor;
    private MockObject|Request $request;
    private InputBag $cookies;

    protected function setUp(): void {
        $this->validatingSplitCookieExtractor = new SplitCookieExtractor([self::JWT_HP_COOKIE_KEY, self::JWT_S_COOKIE_KEY]);
        $this->request = $this->createMock(Request::class);
        $this->cookies = new InputBag();
        $this->request->cookies = $this->cookies;
    }

    public function testReturnValidConcatenatedJWTToken() {
        $jwt = self::JWT_HP.'.'.self::JWT_S;
        $this->cookies->set(self::JWT_HP_COOKIE_KEY, self::JWT_HP);
        $this->cookies->set(self::JWT_S_COOKIE_KEY, self::JWT_S);

        $result = $this->validatingSplitCookieExtractor->extract($this->request);

        self::assertThat($result, self::equalTo($jwt));
    }

    public function testReturnFalseWhenNoCookies() {
        $result = $this->validatingSplitCookieExtractor->extract($this->request);

        self::assertThat($result, self::isFalse());
    }

    public function testReturnFalseWhenDecoratedOnlyReturnsJWTHP() {
        $this->cookies->set(self::JWT_HP_COOKIE_KEY, self::JWT_HP);

        $result = $this->validatingSplitCookieExtractor->extract($this->request);

        self::assertThat($result, self::isFalse());
    }

    public function testReturnFalseWhenDecoratedOnlyReturnsJWTS() {
        $this->cookies->set(self::JWT_S_COOKIE_KEY, self::JWT_S);

        $result = $this->validatingSplitCookieExtractor->extract($this->request);

        self::assertThat($result, self::isFalse());
    }
}
