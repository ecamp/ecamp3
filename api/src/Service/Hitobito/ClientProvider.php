<?php

declare(strict_types=1);

namespace App\Service\Hitobito;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ClientProvider {
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly Security $security,
        private readonly RequestStack $requestStack,
        private readonly AccessTokenProvider $accessTokenProvider,
        private readonly string $pbsmidataBaseUrl,
        private readonly bool $useMockClient = false,
    ) {}

    /**
     * Returns a Hitobito client which is authenticated with the given provider.
     * Fails if no access token cookie is set.
     */
    public function getClientForCurrentUser(HitobitoProvider $provider): ClientInterface {
        $authContext = $this->getAuthContext($provider);

        if ($this->useMockClient) {
            return new MockClient();
        }

        $baseUrl = match ($provider) {
            HitobitoProvider::PBSMIDATA => $this->pbsmidataBaseUrl,
            HitobitoProvider::CEVIDB, HitobitoProvider::JUBLADB => throw new \InvalidArgumentException("Unsupported Hitobito provider \"{$provider->value}\""),
        };

        return new Client($this->client, $baseUrl, $authContext);
    }

    private function getAuthContext(HitobitoProvider $provider): AuthContext {
        return $this->accessTokenProvider->getAccessToken(
            $this->getRequest(),
            $provider,
            $this->getAuthenticatedUser()->getId()
        );
    }

    private function getAuthenticatedUser(): User {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            // This should never happen because it should be caught earlier by our security settings
            // on all API operations using a Hitobito client.
            throw new AccessDeniedHttpException();
        }

        return $user;
    }

    private function getRequest(): Request {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new \LogicException('No current request');
        }

        return $request;
    }
}
