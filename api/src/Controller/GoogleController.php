<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController {
    /**
     * Link to this controller to start the "connect" process.
     *
     * @Route("/auth/google", name="connect_google_start")
     */
    public function connectAction(Request $request, ClientRegistry $clientRegistry) {
        $request->getSession()->set('redirect_uri', $request->get('callback'));

        return $clientRegistry
            ->getClient('google') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect()
        ;
    }

    /**
     * After going to Google, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml.
     *
     * @Route("/auth/google/callback", name="connect_google_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry) {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a custom authenticator
    }
}
