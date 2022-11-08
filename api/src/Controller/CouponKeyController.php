<?php

namespace App\Controller;

use App\Service\CampCouponService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CouponKeyController extends AbstractController {
    public function __construct(private CampCouponService $couponService) {
    }

    #[Route('/coupon', name: 'coupon')]
    public function createAction() {
        // $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return new \Symfony\Component\HttpFoundation\JsonResponse(
            array_map(
                [$this->couponService, 'createCoupon'],
                range(1, 20)
            )
        );
    }
}
