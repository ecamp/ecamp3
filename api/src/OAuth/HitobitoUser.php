<?php

namespace App\OAuth;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class HitobitoUser implements ResourceOwnerInterface {
    /**
     * @var array
     */
    protected $response;

    public function __construct(array $response) {
        $this->response = $response;
    }

    public function getId(): mixed {
        return $this->response['id'];
    }

    /**
     * Get preferred first name.
     */
    public function getFirstName(): ?string {
        return $this->getResponseValue('first_name');
    }

    /**
     * Get preferred last name.
     */
    public function getLastName(): ?string {
        return $this->getResponseValue('last_name');
    }

    /**
     * Get nickname.
     */
    public function getNickName(): ?string {
        return $this->getResponseValue('nickname');
    }

    /**
     * Get email address.
     */
    public function getEmail(): ?string {
        return $this->getResponseValue('email');
    }

    /**
     * Get user data as an array.
     */
    public function toArray(): array {
        return $this->response;
    }

    private function getResponseValue($key) {
        return $this->response[$key] ?? null;
    }
}
