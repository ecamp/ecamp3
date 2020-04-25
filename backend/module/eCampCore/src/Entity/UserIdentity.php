<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Table(name="user_identity", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="identity", columns={"provider", "provider_id"}),
 * })
 * @ORM\Entity
 */
class UserIdentity extends BaseEntity {
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * This is the identity provider (Facebook, Google, etc.)
     * @var string
     * @ORM\Column(name="provider", type="string", length=255, nullable=false)
     */
    private $provider;

    /**
     * This is the ID given by the identity provider
     * @var string
     * @ORM\Column(name="provider_id", type="string", length=255, nullable=false)
     */
    private $providerId;

    /**
     * Set user
     * @param User $user
     */
    public function setUser(User $user) {
        $this->user = $user;
    }

    /**
     * Get user
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set provider
     * @param string $provider
     */
    public function setProvider($provider) {
        $this->provider = $provider;
    }

    /**
     * Get provider
     * @return string
     */
    public function getProvider() {
        return $this->provider;
    }

    /**
     * Set provider ID
     * @param string $providerId
     */
    public function setProviderId($providerId) {
        $this->providerId = $providerId;
    }

    /**
     * Get provider Id
     * @return string
     */
    public function getProviderId() {
        return $this->providerId;
    }
}
