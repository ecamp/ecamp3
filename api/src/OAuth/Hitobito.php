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
    protected $base_url;

    public function getBaseAuthorizationUrl(): string {
        return $this->base_url.'/oauth/authorize';
    }

    public function getBaseAccessTokenUrl(array $params): string {
        return $this->base_url.'/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string {
        return $this->base_url.'/oauth/profile';
    }

    protected function getDefaultScopes(): array {
        return [
            'name',
        ];
    }

    protected function getScopeSeparator(): string {
        return ' ';
    }

    protected function fetchResourceOwnerDetails(AccessToken $token): mixed {
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
            throw new IdentityProviderException($response->getReasonPhrase(), $response->getStatusCode(), $response->getBody());
        }
    }

    protected function getDefaultHeaders(): array {
        return [
            'Accept' => 'application/json',
        ];
    }

    protected function createResourceOwner(array $response, AccessToken $token): HitobitoUser {
        return new HitobitoUser($response);
    }
}
