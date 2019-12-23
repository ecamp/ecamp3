<?php

namespace eCamp\Core\Auth\Provider;

use Hybridauth\Adapter\OAuth2;
use Hybridauth\Data;
use Hybridauth\Exception\UnexpectedApiResponseException;
use Hybridauth\User;

final class Hitobito extends OAuth2 {
    /**
     * Defaults scope to requests
     */
    protected $scope = 'name';

    /**
     * Default Base URL to provider API
     */
    protected $apiBaseUrl = 'https://pbs.puzzle.ch/de/oauth';

    /**
     * Default Authorization Endpoint
     */
    protected $authorizeUrl = 'https://pbs.puzzle.ch/oauth/authorize';

    /**
     * Default Access Token Endpoint
     */
    protected $accessTokenUrl = 'https://pbs.puzzle.ch/oauth/token';

    function getUserProfile() {
        /* Send a signed http request to provider API to request user's profile */
        $response = $this->apiRequest('profile', 'GET', [], ['X-Scope' => 'name']);
        $data = new Data\Collection($response);

        if (! $data->exists('id')) {
            throw new UnexpectedApiResponseException('Provider API returned an unexpected response.');
        }

        $userProfile = new User\Profile();

        $userProfile->identifier  = $data->get('email');
        $userProfile->firstName   = $data->get('first_name');
        $userProfile->lastName    = $data->get('last_name');
        $userProfile->displayName = $data->get('nickname');
        $userProfile->email       = $data->get('email');

        return $userProfile;
    }

    protected function setCallback($callback) {
        if ($callback === 'urn:ietf:wg:oauth:2.0:oob') {
            $this->callback = $callback;
        } else {
            parent::setCallback($callback);
        }
    }

}
