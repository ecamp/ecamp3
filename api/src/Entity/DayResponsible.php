<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
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
    operations: [
        new Get(
            security: 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'
        ),
        new Delete(
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'
        ),
        new GetCollection(
            security: 'is_authenticated()'
        ),
        new Post(
            securityPostDenormalize: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']]
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['day'])]
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
    #[ORM\ManyToOne(targetEntity: Day::class, inversedBy: 'dayResponsibles')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Day $day = null;

    /**
     * The person that is responsible. Must belong to the same camp as the day's period.
     */
    #[AssertBelongsToSameCamp]
    #[ApiProperty(example: '/camp_collaborations/1a2b3c4d')]
    #[Groups(['read', 'write'])]
    #[ORM\ManyToOne(targetEntity: CampCollaboration::class, inversedBy: 'dayResponsibles')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?CampCollaboration $campCollaboration = null;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->day?->getCamp();
    }
}
