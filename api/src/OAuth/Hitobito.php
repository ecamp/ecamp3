<?php

namespace App\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;

class Hitobito extends AbstractProvider {
    use BearerAuthorizationTrait;

    public function getBaseAuthorizationUrl(): string {
        return 'https://pbs.puzzle.ch/oauth/authorize';
    }

    public function getBaseAccessTokenUrl(array $params): string {
        return 'https://pbs.puzzle.ch/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string {
        return 'https://pbs.puzzle.ch/de/oauth/profile';
    }

    protected function getDefaultScopes(): array {
        return [
            'name',
        ];
    }

    protected function getScopeSeparator(): string {
        return ' ';
    }

    protected function fetchResourceOwnerDetails(AccessToken $token) {
        $url = $this->getResourceOwnerDetailsUrl($token);

        $separator = $this->getScopeSeparator();
        $headers = [
            'X-Scope' => implode($separator, $this->getDefaultScopes()),
        ];

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token, ['headers' => $headers]);

        $response = $this->getParsedResponse($request);

        if (false === is_array($response)) {
            throw new UnexpectedValueException(
                'Invalid response received from Authorization Server. Expected JSON.'
            );
        }

        return $response;
    }

    protected function checkResponse(ResponseInterface $response, $data): void {
        if ($response->getStatusCode() >= 400) {
            throw new IdentityProviderException($response->getReasonPhrase(), $response->getStatusCode(), $response);
        }
    }

    protected function getDefaultHeaders() {
        return [
            'Accept' => 'application/json',
        ];
    }

    protected function createResourceOwner(array $response, AccessToken $token): HitobitoUser {
        return new HitobitoUser($response);
    }
}
