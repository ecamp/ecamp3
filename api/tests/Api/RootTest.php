<?php

namespace App\Tests\Api;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @internal
 */
class RootTest extends ECampApiTestCase {
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testOptionsWhenNotLoggedIn() {
        static::createBasicClient()->request(
            'OPTIONS',
            '/',
            [
                'headers' => [
                    'Origin' => 'http://localhost:3000',
                    'Access-Control-Request-Method' => 'GET',
                    'Access-Control-Request-Headers' => 'Origin, Content-Type, Accept, Authorization',
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(200);
    }
}
