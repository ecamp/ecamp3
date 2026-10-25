<?php

namespace App\Tests\Api\HttpCache;

use App\Tests\Api\ECampApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
class CacheScopeTest extends ECampApiTestCase {
    private const HEADER = 'x-cache-scope';

    #[DataProvider('scopedRequests')]
    public function testResponseIsScopedToCampOfResource(string $urlTemplate, string $fixture, string $campFixture) {
        $id = static::getFixture($fixture)->getId();

        $response = static::createClientWithCredentials()->request('GET', str_replace('{id}', $id, $urlTemplate));

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame([$this->getIriFor($campFixture)], $response->getHeaders()[self::HEADER]);
    }

    public static function scopedRequests(): array {
        return [
            'activity' => ['/activities/{id}', 'activity1', 'camp1'],
            'camp activities' => ['/camps/{id}/activities', 'camp1', 'camp1'],
            'camp categories' => ['/camps/{id}/categories', 'camp1', 'camp1'],
            'camp checklists' => ['/camps/{id}/checklists', 'camp1', 'camp1'],
            'period days' => ['/periods/{id}/days', 'period1', 'camp1'],
            'period schedule entries' => ['/periods/{id}/schedule_entries', 'period1', 'camp1'],
        ];
    }

    public function testContentTypesArePublic() {
        $response = static::createClientWithCredentials()->request('GET', '/content_types');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame(['public'], $response->getHeaders()[self::HEADER]);
    }

    public function testResponseWithoutDeclaredScopeHasNoScope() {
        $activity = static::getFixture('activity1');

        $response = static::createClientWithCredentials()->request('GET', '/activities/'.$activity->getId().'/comments');

        $this->assertResponseStatusCodeSame(200);
        $this->assertArrayNotHasKey(self::HEADER, $response->getHeaders());
    }

    public function testDeniedResponseHasNoScope() {
        $activity = static::getFixture('activity1');

        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/activities/'.$activity->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertArrayNotHasKey(self::HEADER, $response->getHeaders(false));
    }
}
