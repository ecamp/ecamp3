<?php

namespace App\Tests\Api;

/**
 * @internal
 */
class RootTest extends ECampApiTestCase {
    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function testOptionsWhenNotLoggedIn() {
        static::createBasicClient()->request('OPTIONS', '/');
        $this->assertResponseStatusCodeSame(200);
    }
}
