<?php

namespace App\Tests\Api\BaseEntity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Entity\Camp;
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
class CreateBaseEntityTest extends ECampApiTestCase {
    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testIdIsNotWritable() {
        static::createClientWithCredentials()->request(
            'POST',
            '/camps',
            [
                'json' => $this->getExampleWritePayload([
                    'id' => '3852c489ace7',
                ]),
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
    public function testCreateTimeIsNotWritable() {
        static::createClientWithCredentials()->request(
            'POST',
            '/camps',
            [
                'json' => $this->getExampleWritePayload([
                    'createTime' => '2023-05-01T00:00:00+00:00',
                ]),
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
    public function testUpdateTimeTimeIsNotWritable() {
        static::createClientWithCredentials()->request(
            'POST',
            '/camps',
            [
                'json' => $this->getExampleWritePayload([
                    'updateTime' => '2023-05-01T00:00:00+00:00',
                ]),
            ]
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("updateTime" is unknown).',
        ]);
    }

    public function getExampleWritePayload($attributes = [], $except = []): array {
        return $this->getExamplePayload(Camp::class, Post::class, $attributes, ['campPrototype'], $except);
    }

    public function getExampleReadPayload($attributes = [], $except = []): array {
        return $this->getExamplePayload(
            Camp::class,
            Get::class,
            $attributes,
            ['periods'],
            $except
        );
    }
}
