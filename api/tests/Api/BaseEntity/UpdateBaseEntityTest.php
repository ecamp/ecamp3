<?php

namespace App\Tests\Api\BaseEntity;

use App\Tests\Api\ECampApiTestCase;

/**
 * Tests for the BaseEntity properties on the example of Camp.
 *
 * @internal
 */
class UpdateBaseEntityTest extends ECampApiTestCase {
    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function testIdNotWriteable() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request(
            'PATCH',
            '/camps/'.$camp->getId(),
            ['json' => [
                'id' => '3852c489ace7',
            ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("id" is unknown).',
        ]);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function testCreateTimeNotWriteable() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request(
            'PATCH',
            '/camps/'.$camp->getId(),
            ['json' => [
                'createTime' => '2023-05-01T00:00:00+00:00',
            ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("createTime" is unknown).',
        ]);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function testUpdateTimeNotWriteable() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request(
            'PATCH',
            '/camps/'.$camp->getId(),
            ['json' => [
                'updateTime' => '2023-05-01T00:00:00+00:00',
            ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("updateTime" is unknown).',
        ]);
    }
}
