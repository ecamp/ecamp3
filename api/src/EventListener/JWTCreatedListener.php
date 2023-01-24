<?php

namespace App\EventListener;

use ApiPlatform\Api\IriConverterInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Security\Core\Security;

/**
 * Adds the IRI of the newly logged in user to the JWT token payload. This is useful for frontends
 * to know where to fetch personal profile information.
 */
class JWTCreatedListener {
    public function __construct(private Security $security, private IriConverterInterface $iriConverter) {
    }

    public function onJWTCreated(JWTCreatedEvent $event) {
        $payload = $event->getData();

        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        $payload['user'] = $this->iriConverter->getIriFromResource($user);
        $event->setData($payload);
    }
}
