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
    protected $scope = 'email name with_roles api_access';

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
        $data = new Data\Collection($response);

        if (! $data->exists('id')) {
            throw new UnexpectedApiResponseException('Provider API returned an unexpected response.');
        }

        $userProfile = new User\Profile();

        $userProfile->identifier  = $data->get('id');
        $userProfile->firstName   = $data->filter('name')->get('givenName');
        $userProfile->lastName    = $data->filter('name')->get('familyName');
        $userProfile->displayName = $data->get('displayName');
        $userProfile->photoURL    = $data->get('image');
        $userProfile->profileURL  = $data->get('url');
        $userProfile->description = $data->get('aboutMe');
        $userProfile->gender      = $data->get('gender');
        $userProfile->language    = $data->get('language');
        $userProfile->email       = $data->get('email');
        $userProfile->phone       = $data->get('phone');
        $userProfile->country     = $data->get('country');
        $userProfile->region      = $data->get('region');
        $userProfile->zip         = $data->get('zip');

        $userProfile->emailVerified = $data->get('verified') ? $userProfile->email : '';

        if ($data->filter('image')->exists('url')) {
            $photoSize = $this->config->get('photo_size') ?: '150';
            $userProfile->photoURL = substr($data->filter('image')->get('url'), 0, -2) . $photoSize;
        }

        if (! $userProfile->email && $data->exists('emails')) {
            $userProfile = $this->fetchUserEmail($userProfile, $data);
        }

        if (! $userProfile->profileURL && $data->exists('urls')) {
            $userProfile = $this->fetchUserProfileUrl($userProfile, $data);
        }

        if (! $userProfile->profileURL && $data->exists('urls')) {
            $userProfile = $this->fetchBirthday($userProfile, $data->get('birthday'));
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
