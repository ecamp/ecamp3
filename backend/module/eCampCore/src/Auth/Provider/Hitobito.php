<?php

namespace eCamp\Core\Auth\Provider;

use Hybridauth\Adapter\OAuth2;
use Hybridauth\Data;
use Hybridauth\Exception\UnexpectedValueException;
use Hybridauth\User;

final class Hitobito extends OAuth2 {
    /**
     * Defaults scope to requests
     */
    protected $scope = 'email';

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
        $response = $this->apiRequest('profile');

        var_dump($response);
        return null;

        $collection = new Data\Collection($response);

        $userProfile = new User\Profile();

        if (!$collection->exists('id')) {
            throw new UnexpectedValueException('Provider API returned an unexpected response.');
        }

        $userProfile->identifier = $collection->get('id');
        $userProfile->email = $collection->get('email');
        $userProfile->displayName = $collection->get('firstName') . ' ' . $collection->get('lastName');
        $userProfile->address = $collection->filter('address')->get('streetAddress');
        $userProfile->city = $collection->filter('address')->get('city');

        if ($collection->exists('image')) {
            $userProfile->photoURL = 'http://provider.ltd/users/' . $collection->get('image');
        }

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
