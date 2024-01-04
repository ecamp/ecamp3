<?php

namespace App\Tests\Api\BaseEntity;

use App\Tests\Api\ECampApiTestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Tests for the BaseEntity properties on the example of Camp.
 *
 * @internal
 */
class UpdateBaseEntityTest extends ECampApiTestCase {
    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
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
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
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
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
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
