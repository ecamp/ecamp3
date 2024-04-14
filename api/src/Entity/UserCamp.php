<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * view_user_camps
 * List all visible camps for each user.
 */
#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'view_user_camps')]
class UserCamp {
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32, nullable: false)]
    public string $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userCamps')]
    public User $user;

    #[ORM\ManyToOne(targetEntity: Camp::class, inversedBy: 'userCamps')]
    public Camp $camp;
}
