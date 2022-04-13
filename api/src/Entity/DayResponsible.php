<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\DayResponsibleRepository;
use App\Validator\AssertBelongsToSameCamp;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A person that has some whole-day responsibility on a day in the camp.
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_authenticated()'],
        'post' => ['security_post_denormalize' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
    ],
    itemOperations: [
        'get' => ['security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'],
        'delete' => ['security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[ApiFilter(SearchFilter::class, properties: ['day'])]
#[UniqueEntity(
    fields: ['campCollaboration', 'day'],
    message: 'This campCollaboration (user) is already responsible for this day.',
)]
#[ORM\Entity(repositoryClass: DayResponsibleRepository::class)]
#[ORM\UniqueConstraint(name: 'day_campCollaboration_unique', columns: ['dayId', 'campCollaborationId'])]
class DayResponsible extends BaseEntity implements BelongsToCampInterface {
    /**
     * The day on which the person is responsible.
     */
    #[Assert\NotNull]
    #[ApiProperty(example: '/days/1a2b3c4d')]
    #[Groups(['read', 'write'])]
    #[ORM\ManyToOne(targetEntity: 'Day', inversedBy: 'dayResponsibles')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Day $day = null;

    /**
     * The person that is responsible. Must belong to the same camp as the day's period.
     */
    #[AssertBelongsToSameCamp]
    #[ApiProperty(example: '/camp_collaborations/1a2b3c4d')]
    #[Groups(['read', 'write'])]
    #[ORM\ManyToOne(targetEntity: 'CampCollaboration', inversedBy: 'dayResponsibles')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?CampCollaboration $campCollaboration = null;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->day?->getCamp();
    }
}
