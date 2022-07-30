<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\ContentNode;
use App\Repository\StoryboardRepository;
use App\Util\EntityMap;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    routePrefix: '/content_node',
    collectionOperations: [
        'get' => [
            'security' => 'is_authenticated()',
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
        'delete' => ['security' => '(is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)) and object.parent !== null'], // disallow delete when contentNode is a root node
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[ORM\Entity(repositoryClass: StoryboardRepository::class)]
#[ORM\Table(name: 'content_node_storyboard')]
class Storyboard extends ContentNode {
    #[ApiProperty(readableLink: true, writableLink: false)]
    #[Groups(['read'])]
    #[ORM\OneToMany(targetEntity: StoryboardSection::class, mappedBy: 'storyboard', orphanRemoval: true, cascade: ['persist'])]
    public Collection $sections;

    public function __construct() {
        parent::__construct();
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

    /**
     * @param Storyboard $prototype
     * @param EntityMap  $entityMap
     */
    public function copyFromPrototype($prototype, $entityMap): void {
        parent::copyFromPrototype($prototype, $entityMap);

        // copy all storyboard sections
        foreach ($prototype->sections as $sectionPrototype) {
            $section = new StoryboardSection();
            $this->addSection($section);

            $section->copyFromPrototype($sectionPrototype, $entityMap);
        }
    }
}
