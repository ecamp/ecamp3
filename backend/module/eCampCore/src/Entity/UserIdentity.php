<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="identity", columns={"provider", "providerId"}),
 * })
 */
class UserIdentity extends BaseEntity {
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?User $user = null;

    /**
     * This is the identity provider (Facebook, Google, etc.).
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private ?string $provider = null;

    /**
     * This is the ID given by the identity provider.
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private ?string $providerId = null;

    /**
     * Set user.
     */
    public function setUser(?User $user) {
        $this->user = $user;
    }

    /**
     * Get user.
     */
    public function getUser(): ?User {
        return $this->user;
    }

    /**
     * Set provider.
     */
    public function setProvider(?string $provider) {
        $this->provider = $provider;
    }

    /**
     * Get provider.
     */
    public function getProvider(): ?string {
        return $this->provider;
    }

    /**
     * Set provider ID.
     *
     * @param string $providerId
     */
    public function setProviderId(?string $providerId) {
        $this->providerId = $providerId;
    }

    /**
     * Get provider Id.
     */
    public function getProviderId(): ?string {
        return $this->providerId;
    }
}
