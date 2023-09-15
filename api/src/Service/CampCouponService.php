<?php

namespace App\Service;

use App\Entity\Camp;
use Doctrine\ORM\EntityManagerInterface;

class CampCouponService {
    public const NO_COUPON_KEY_REQUIRED_MESSAGE = 'No Coupon-Key required';

    public function __construct(
        private string $secret,
        private EntityManagerInterface $em
    ) {}

    public function createCoupon() {
        if (!$this->isActive()) {
            return self::NO_COUPON_KEY_REQUIRED_MESSAGE;
        }
        $secret = intval($this->secret);
        $min = intval(ceil(78364164096 / $secret));
        $max = intval(floor(2821109907455 / $secret));
        $rnd = mt_rand($min, $max) * $secret;
        $coupon = base_convert(strval($rnd), 10, 36);

        return substr($coupon, 0, 4).'-'.substr($coupon, 4, 4);
    }

    public function verifyCoupon($couponKey) {
        // Basically, you should not program any security algorithms yourself. However,
        // since we want to have short coupon keys, we implement our own algorithm here.
        // In the long run the CampCouponService will be deleted again.

        if (!$this->isActive()) {
            return true;
        }

        // ensure correct format
        if (!preg_match('/([0-9a-z]{4})-([0-9a-z]{4})/', $couponKey, $matches)) {
            return false;
        }

        $couponKey = $matches[1].$matches[2];
        $couponKey = base_convert($couponKey, 36, 10);

        // Invalid CouponKey
        return 0 == intval($couponKey) % intval($this->secret);
    }

    public function isCouponFree($couponKey) {
        // CouponKey is already used
        $q = $this->em->createQueryBuilder();
        $q->select('count(c.id)')->from(Camp::class, 'c')
            ->where('c.couponKey = :couponKey')
            ->setParameter('couponKey', $couponKey)
        ;
        $cnt = $q->getQuery()->getSingleScalarResult();

        return 0 == $cnt;
    }

    public function isActive(): bool {
        return '' != $this->secret;
    }
}
