<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * view_user_camps_with_public
 * List all visible camps for each user, through camp collaborations or because
 * they are prototype or shared camps.
 */
#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'view_user_camps_with_public')]
class UserCampWithPublic {
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 32, nullable: false)]
    public string $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userCampsWithPublic')]
    public User $user;

    #[ORM\ManyToOne(targetEntity: Camp::class, inversedBy: 'userCampsWithPublic')]
    public Camp $camp;
}
