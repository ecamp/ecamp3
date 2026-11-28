<?php

namespace App\Entity;

use App\Repository\OAuthStateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Database table used for storing valid OAuth state tokens and their expiration dates.
 * Definitely not exposed on the API, this is only for internal security checking.
 */
#[ORM\Entity(repositoryClass: OAuthStateRepository::class)]
#[ORM\Index(name: 'IDX_466EF70CD49BE2B9A393D2FB', columns: ['expireTime', 'state'])]
class OAuthState extends BaseEntity {
    #[ORM\Column(type: 'string', length: 32)]
    public string $state;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $expireTime;
}
