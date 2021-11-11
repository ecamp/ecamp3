<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\ContentNode;
use App\Repository\StoryboardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    routePrefix: '/content_node',
    collectionOperations: [
        'get' => [
            'security' => 'is_fully_authenticated()',
        ],
        'post' => [
            'denormalization_context' => ['groups' => ['write', 'create']],
            'security_post_denormalize' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            'validation_groups' => ['Default', 'create'],
        ],
    ],
    itemOperations: [
        'get' => ['security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'],
        'patch' => [
            'denormalization_context' => ['groups' => ['write', 'update']],
            'security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            'validation_groups' => ['Default', 'update'],
        ],
        'delete' => ['security' => '(is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)) and object.owner === null'], // disallow delete when contentNode is a root node
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[Entity(repositoryClass: StoryboardRepository::class)]
#[Table(name: 'content_node_storyboard')]
class Storyboard extends ContentNode {
    #[ApiProperty(readableLink: true, writableLink: false)]
    #[Groups(['read'])]
    #[OneToMany(targetEntity: 'StoryboardSection', mappedBy: 'storyboard', orphanRemoval: true, cascade: ['persist'])]
    public Collection $sections;

    public function __construct() {
        $this->sections = new ArrayCollection();

        parent::__construct();
    }

    /**
     * @return StoryboardSection[]
     */
    public function getSections(): array {
        return $this->sections->getValues();
    }

    public function addSection(StoryboardSection $section): self {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
            $section->storyboard = $this;
        }

        return $this;
    }

    public function removeSection(StoryboardSection $section): self {
        if ($this->sections->removeElement($section)) {
            if ($section->storyboard === $this) {
                $section->storyboard = null;
            }
        }

        return $this;
    }
}
