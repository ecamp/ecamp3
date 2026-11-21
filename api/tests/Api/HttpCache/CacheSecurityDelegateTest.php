<?php

namespace App\Tests\Api\HttpCache;

use App\Tests\Api\ECampApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
class CacheSecurityDelegateTest extends ECampApiTestCase {
    private const HEADER_SECURITY_DELEGATE = 'x-cache-security-delegate';
    private const HEADER_MODE = 'x-cache-mode';

    #[DataProvider('delegatingRequests')]
    public function testResponseDelegatesSecurityToCampOfResource(string $urlTemplate, string $fixture, string $campFixture) {
        $id = static::getFixture($fixture)->getId();

        $response = static::createClientWithCredentials()->request('GET', str_replace('{id}', $id, $urlTemplate));

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame(['shared'], $response->getHeaders()[self::HEADER_MODE]);
        $this->assertSame([$this->getIriFor($campFixture)], $response->getHeaders()[self::HEADER_SECURITY_DELEGATE]);
    }

    public static function delegatingRequests(): array {
        return [
            'activity' => ['/activities/{id}', 'activity1', 'camp1'],
            'camp activities' => ['/camps/{id}/activities', 'camp1', 'camp1'],
            'camp checklists' => ['/camps/{id}/checklists', 'camp1', 'camp1'],
            'period days' => ['/periods/{id}/days', 'period1', 'camp1'],
            'period schedule entries' => ['/periods/{id}/schedule_entries', 'period1', 'camp1'],
            'activity of another camp' => ['/activities/{id}', 'activity1camp2', 'camp2'],
            'schedule entries of another camp' => ['/periods/{id}/schedule_entries', 'period1camp2', 'camp2'],
        ];
    }

    public function testCategoriesAreCachedPerUser() {
        $id = static::getFixture('camp1')->getId();

        $response = static::createClientWithCredentials()->request('GET', "/camps/{$id}/categories");

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame(['user'], $response->getHeaders()[self::HEADER_MODE]);
        $this->assertArrayNotHasKey(self::HEADER_SECURITY_DELEGATE, $response->getHeaders());
        $this->assertMatchesRegularExpression('/^max-age=0, public, s-maxage=\d+$/', $response->getHeaders()['cache-control'][0]);
    }

    public function testPerUserResponseDoesNotOutliveTheToken() {
        $id = static::getFixture('camp1')->getId();

        $response = static::createClientWithCredentials()->request('GET', "/camps/{$id}/categories");

        preg_match('/s-maxage=(\d+)/', $response->getHeaders()['cache-control'][0], $matches);
        $this->assertGreaterThan(0, (int) $matches[1]);
        $this->assertLessThanOrEqual(43200, (int) $matches[1]);
    }

    public function testSharedResponseIsCachedForAllUsers() {
        $id = static::getFixture('camp1')->getId();

        $response = static::createClientWithCredentials()->request('GET', "/camps/{$id}/activities");

        $this->assertSame('Accept, Content-Type, Authorization, Origin', $response->getHeaders()['vary'][0]);
        $this->assertSame('max-age=0, public, s-maxage=43200', $response->getHeaders()['cache-control'][0]);
    }

    public function testResponseWithoutCacheDeclarationIsNotCacheable() {
        $activity = static::getFixture('activity1');

        $response = static::createClientWithCredentials()->request('GET', "/activities/{$activity->getId()}/comments");

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString('no-cache', $response->getHeaders()['cache-control'][0]);
        $this->assertStringNotContainsString('public', $response->getHeaders()['cache-control'][0]);
    }

    public function testContentTypesArePublic() {
        $response = static::createClientWithCredentials()->request('GET', '/content_types');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame(['public'], $response->getHeaders()[self::HEADER_MODE]);
        $this->assertArrayNotHasKey(self::HEADER_SECURITY_DELEGATE, $response->getHeaders());
    }

    public function testContentTypeIsPublic() {
        $contentType = static::getFixture('contentTypeSafetyConsiderations');

        $response = static::createClientWithCredentials()->request('GET', '/content_types/'.$contentType->getId());

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame(['public'], $response->getHeaders()[self::HEADER_MODE]);
        $this->assertArrayNotHasKey(self::HEADER_SECURITY_DELEGATE, $response->getHeaders());
    }

    public function testEntrypointIsPublic() {
        $response = static::createClientWithCredentials()->request('GET', '/');

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame(['public'], $response->getHeaders()[self::HEADER_MODE]);
        $this->assertArrayNotHasKey(self::HEADER_SECURITY_DELEGATE, $response->getHeaders());
    }

    public function testResponseWithoutDeclaredDelegateHasNoDelegate() {
        $activity = static::getFixture('activity1');

        $response = static::createClientWithCredentials()->request('GET', '/activities/'.$activity->getId().'/comments');

        $this->assertResponseStatusCodeSame(200);
        $this->assertArrayNotHasKey(self::HEADER_SECURITY_DELEGATE, $response->getHeaders());
        $this->assertArrayNotHasKey(self::HEADER_MODE, $response->getHeaders());
    }

    public function testDeniedResponseHasNoDelegate() {
        $activity = static::getFixture('activity1');

        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/activities/'.$activity->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertArrayNotHasKey(self::HEADER_SECURITY_DELEGATE, $response->getHeaders(false));
        $this->assertArrayNotHasKey(self::HEADER_MODE, $response->getHeaders(false));
    }
}
