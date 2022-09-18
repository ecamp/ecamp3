<?php

namespace App\Tests\Api\BaseEntity;

use App\Entity\Camp;
use App\Tests\Api\ECampApiTestCase;

/**
 * Tests for the BaseEntity properties on the example of Camp.
 *
 * @internal
 */
class CreateBaseEntityTest extends ECampApiTestCase {
    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
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
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
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
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
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
        return $this->getExamplePayload(Camp::class, '/camps', 'post', $attributes, [], $except);
    }

    public function getExampleReadPayload($attributes = [], $except = []): array {
        return $this->getExamplePayload(
            Camp::class,
            '/camps',
            'get',
            $attributes,
            ['periods'],
            $except
        );
    }
}
