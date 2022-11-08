<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CouponKeyController extends AbstractController {
    private $secret = 'test';

    /**
     * @Route("/coupon", name="coupon")
     */
    public function createAction() {
        return new \Symfony\Component\HttpFoundation\JsonResponse(
            array_map(
                [$this, 'createCoupon'],
                range(1, 20)
            )
        );
    }

    private function createCoupon() {
        return base64_encode(password_hash($this->secret, PASSWORD_BCRYPT, ['cost' => 4]));
    }
}
