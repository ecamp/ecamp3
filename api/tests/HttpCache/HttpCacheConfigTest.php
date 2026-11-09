<?php

namespace App\Tests\HttpCache;

use ApiPlatform\Metadata\Get;
use App\HttpCache\CacheMode;
use App\HttpCache\HttpCacheConfig;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class HttpCacheConfigTest extends TestCase {
    public function testIsNotCachedByDefault() {
        $this->assertNull($this->parse(null));
        $this->assertNull($this->parse(false));
    }

    public function testTrueIsCachedPerUser() {
        $config = $this->parse(true);

        $this->assertSame(CacheMode::User, $config->mode);
        $this->assertNull($config->securityDelegate);
    }

    public function testUserModeIsTheDefaultMode() {
        $this->assertSame(CacheMode::User, $this->parse([])->mode);
        $this->assertSame(CacheMode::User, $this->parse(['mode' => 'user'])->mode);
    }

    public function testSharedModeHasSecurityDelegate() {
        $config = $this->parse(['mode' => 'shared', 'security_delegate' => 'object.getCamp()']);

        $this->assertSame(CacheMode::Shared, $config->mode);
        $this->assertSame('object.getCamp()', $config->securityDelegate);
    }

    public function testPublicMode() {
        $this->assertSame(CacheMode::Public, $this->parse(['mode' => 'public'])->mode);
    }

    #[DataProvider('invalidDeclarations')]
    public function testRejectsInvalidDeclaration(mixed $declared) {
        $this->expectException(\LogicException::class);

        $this->parse($declared);
    }

    public static function invalidDeclarations(): array {
        return [
            'not true or an array' => ['shared'],
            'unknown mode' => [['mode' => 'private']],
            'unknown option' => [['mode' => 'user', 'ttl' => 10]],
            'shared without delegate' => [['mode' => 'shared']],
            'shared with empty delegate' => [['mode' => 'shared', 'security_delegate' => '']],
            'user with delegate' => [['mode' => 'user', 'security_delegate' => 'camp']],
            'default mode with delegate' => [['security_delegate' => 'camp']],
            'public with delegate' => [['mode' => 'public', 'security_delegate' => 'camp']],
        ];
    }

    private function parse(mixed $declared): ?HttpCacheConfig {
        return HttpCacheConfig::fromOperation(new Get(name: 'test', extraProperties: [HttpCacheConfig::EXTRA_PROPERTY => $declared]));
    }
}
