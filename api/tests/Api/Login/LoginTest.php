<?php

namespace App\Tests\Api\Login;

use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @internal
 */
class LoginTest extends ECampApiTestCase {
    public const PASSWORD = 'password';

    /**
     * @throws TransportExceptionInterface
     */
    public function testLoginWithUnknownUser() {
        static::createBasicClient()->request(
            'POST',
            '/authentication_token',
            [
                'json' => [
                    'identifier' => 'unknown',
                    'password' => self::PASSWORD,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testLoginWrongPassword() {
        /** @var User $user */
        $user = static::$fixtures['user1manager'];

        static::createBasicClient()->request(
            'POST',
            '/authentication_token',
            [
                'json' => [
                    'identifier' => $user->getEmail(),
                    'password' => 'wrongPassword',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testLoginWithNoPassword() {
        /** @var User $user */
        $user = static::$fixtures['user1manager'];

        static::createBasicClient()->request(
            'POST',
            '/authentication_token',
            [
                'json' => [
                    'identifier' => $user->getEmail(),
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(400);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testLoginWithNoIdentifier() {
        static::createBasicClient()->request(
            'POST',
            '/authentication_token',
            [
                'json' => [
                    'password' => 'password',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(400);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testLoginWithValidCredentials() {
        /** @var User $user */
        $user = static::$fixtures['user1manager'];

        $this->createBasicClient()->request(
            'POST',
            '/authentication_token',
            [
                'json' => [
                    'identifier' => $user->getEmail(),
                    'password' => 'test',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(204);
        $this->assertResponseHasHeader('Set-Cookie');
    }
}
