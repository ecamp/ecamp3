<?php

namespace App\Tests\Integration\Service;

use App\Service\CampCouponService;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\isFalse;
use function PHPUnit\Framework\isTrue;
use function PHPUnit\Framework\logicalNot;

/**
 * @internal
 */
class CampCouponServiceTest extends KernelTestCase {
    private EntityManagerInterface $entityManager;

    protected function setUp(): void {
        self::bootKernel();
        parent::setUp();

        /** @var EntityManagerInterface $obj */
        $obj = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $this->entityManager = $obj;
    }

    public function testCreatesNoTokensWhenSecretIsEmpty() {
        $campCouponService = $this->createService('');

        $coupon = $campCouponService->createCoupon();

        assertThat($coupon, equalTo(CampCouponService::NO_COUPON_KEY_REQUIRED_MESSAGE));
    }

    public function testCreateToken() {
        $campCouponService = $this->createService('10000');

        $coupon = $campCouponService->createCoupon();

        assertThat(strlen($coupon), equalTo(9));
        assertThat($coupon, logicalNot(equalTo(CampCouponService::NO_COUPON_KEY_REQUIRED_MESSAGE)));
    }

    public function testCreateDifferentTokensForEachCall() {
        $campCouponService = $this->createService('1000000');

        $coupon = $campCouponService->createCoupon();
        $coupon2 = $campCouponService->createCoupon();

        assertThat($coupon, logicalNot(equalTo($coupon2)));
    }

    /**
     * @dataProvider someRandomTokens()
     */
    public function testReturnsTrueForAllTokensWhenSecretIsEmpty(string $token) {
        $campCouponService = $this->createService('');

        assertThat($campCouponService->verifyCoupon($token), isTrue());
    }

    /**
     * @dataProvider someRandomTokens()
     */
    public function testReturnsFalseForRandomTokensIfSecretIsNotEmpty(string $token) {
        $campCouponService = $this->createService('45983');

        assertThat($campCouponService->verifyCoupon($token), isFalse());
    }

    /**
     * @dataProvider someRandomSecrets()
     */
    public function testReturnsTrueForValidTokens(string $secret) {
        $campCouponService = $this->createService($secret);

        $coupon = $campCouponService->createCoupon();

        assertThat($campCouponService->verifyCoupon($coupon), isTrue());
    }

    public static function someRandomTokens(): array {
        $emptyString = '';
        // created with secret 1000000
        $token1 = 'l593-8qps';
        // created with secret 1000000
        $token2 = '84jq-m35s';
        $uuid = Uuid::uuid4()->toString();

        return [
            $emptyString => [$emptyString],
            $token1 => [$token1],
            $token2 => [$token2],
            $uuid => [$uuid],
        ];
    }

    public static function someRandomSecrets(): array {
        $parameters = [];
        for ($__ = 0; $__ < 10; ++$__) {
            $secret = rand(1000, 1000000);
            for ($i = 0; $i < 10; ++$i) {
                $parameters["{$secret} {$i}"] = ["{$secret}"];
            }
        }

        return $parameters;
    }

    private function createService(string $secret): CampCouponService {
        return new CampCouponService($secret, $this->entityManager);
    }
}
