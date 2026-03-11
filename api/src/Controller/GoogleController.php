<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class GoogleController extends AbstractController {
    public function __construct(private readonly ClientRegistry $clientRegistry) {}

    /**
     * Link to this controller to start the "connect" process.
     */
    #[Route('/auth/google', name: 'connect_google_start')]
    public function connect(Request $request) {
        return $this->clientRegistry
            ->getClient('google') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([], ['additionalData' => ['callback' => $request->get('callback')]])
        ;
    }

    /**
     * After going to Google, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml.
     */
    #[Route('/auth/google/callback', name: 'connect_google_check')]
    public function connectCheck() {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a custom authenticator
    }
}
