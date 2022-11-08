<?php

namespace App\Service;

class CampCouponService {
    public function __construct(private string $secret) {
    }

    public function createCoupon() {
        if ('' == $this->secret) {
            return 'No Coupon-Key required';
        }

        return base64_encode(password_hash($this->secret, PASSWORD_BCRYPT, ['cost' => 8]));
    }

    public function verifyCoupon($couponKey) {
        if ('' == $this->secret) {
            return true;
        }

        return password_verify($this->secret, base64_decode($couponKey));
    }
}
