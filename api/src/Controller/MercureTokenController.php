<?php

declare(strict_types=1);

namespace App\Controller;

use App\Security\ResourceReadAccessChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubRegistry;
use Symfony\Component\Mercure\Jwt\Grant;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Issues short-lived Mercure subscriber tokens. A token is only valid for a single topic, which is the IRI of a
 * resource that the current user is allowed to read. The login JWT cannot be used for this, because it does not
 * contain any topics.
 *
 * The hub only checks the token when a connection is established, so clients have to request a new token
 * whenever they (re)connect.
 */
class MercureTokenController extends AbstractController {
    public const TOKEN_TTL_SECONDS = 300;

    public function __construct(
        private readonly ResourceReadAccessChecker $accessChecker,
        private readonly HubRegistry $hubRegistry,
    ) {}

    #[Route('/mercure/token', name: 'mercure_token', methods: ['POST'])]
    public function token(Request $request): JsonResponse {
        $topic = $request->getPayload()->get('topic');
        if (!is_string($topic) || '' === $topic) {
            return $this->error(Response::HTTP_BAD_REQUEST, 'The "topic" property is required and must be a string.');
        }

        try {
            $access = $this->accessChecker->check($topic);
        } catch (\InvalidArgumentException) {
            return $this->error(Response::HTTP_NOT_FOUND, 'The topic does not reference an existing resource.');
        }

        if (!$access->granted) {
            return $this->error(Response::HTTP_FORBIDDEN, 'Access to this topic is denied.');
        }

        $expiresAt = new \DateTimeImmutable('@'.(time() + self::TOKEN_TTL_SECONDS));
        $token = $this->hubRegistry->getHub()->getFactory()?->create(
            [new Grant([Grant::ACTION_SUBSCRIBE], [$topic])],
            ['exp' => $expiresAt, 'sub' => $this->getUser()?->getUserIdentifier() ?? 'anonymous']
        );
        if (null === $token) {
            return $this->error(Response::HTTP_INTERNAL_SERVER_ERROR, 'The Mercure hub is not configured to issue tokens.');
        }

        return new JsonResponse(['token' => $token, 'expiresAt' => $expiresAt->format(\DateTimeInterface::ATOM)]);
    }

    private function error(int $status, string $message): JsonResponse {
        return new JsonResponse(['title' => 'An error occurred', 'detail' => $message], $status);
    }
}
