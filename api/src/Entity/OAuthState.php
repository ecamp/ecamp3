<?php

namespace App\Entity;

use App\Repository\OAuthStateRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Database table used for storing valid OAuth state tokens and their expiration dates.
 * Definitely not exposed on the API, this is only for internal security checking.
 */
#[ORM\Entity(repositoryClass: OAuthStateRepository::class)]
#[ORM\Index(columns: ['expireTime', 'state'])]
class OAuthState extends BaseEntity {
    #[ORM\Column(type: 'string')]
    public string $state;

    #[ORM\Column(type: 'datetime')]
    public DateTime $expireTime;
}
