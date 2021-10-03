<?php

namespace App\Controller;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController {

    public function __construct(private UrlGeneratorInterface $urlGenerator) {
    }

    /**
     * @Route("/auth", name="index_auth", methods={"GET"})
     */
    public function indexAction() {
        return $this->json([
            '_links' => [
                'self' => ['href' => $this->urlGenerator->generate('index_auth')],
                'login' => ['href' => $this->urlGenerator->generate('authentication_token')],
                'google' => ['href' => $this->urlGenerator->generate('connect_google_start').'{?callback}', 'templated' => true],
                'pbsmidata' => ['href' => $this->urlGenerator->generate('connect_hitobito_start').'{?callback}', 'templated' => true],
            ],
        ]);
    }
}
