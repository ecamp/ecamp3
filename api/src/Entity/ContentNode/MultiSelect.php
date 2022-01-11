<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\ContentNode;
use App\Repository\MultiSelectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MultiSelectRepository::class)
 * @ORM\Table(name="content_node_multiselect")
 */
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
        'delete' => ['security' => '(is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)) and object.owner === null'], // disallow delete when contentNode is a root node
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
class MultiSelect extends ContentNode {
    /**
     * @ORM\OneToMany(targetEntity="MultiSelectOption", mappedBy="multiSelect", orphanRemoval=true, cascade={"persist"})
     */
    #[ApiProperty(readableLink: true, writableLink: false)]
    #[Groups(['read'])]
    public Collection $options;

    public function __construct() {
        $this->options = new ArrayCollection();

        parent::__construct();
    }

    /**
     * @return MultiSelectOption[]
     */
    public function getOptions(): array {
        return $this->options->getValues();
    }

    public function addOption(MultiSelectOption $option): self {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
            $option->multiSelect = $this;
        }

        return $this;
    }

    public function removeOption(MultiSelectOption $option): self {
        if ($this->options->removeElement($option)) {
            if ($option->multiSelect === $this) {
                $option->multiSelect = null;
            }
        }

        return $this;
    }

    /**
     * @param MultiSelect $prototype
     */
    public function copyFromPrototype($prototype) {
        // copy all multiSelect options
        foreach ($prototype->options as $prototypeOption) {
            $option = new MultiSelectOption();

            $option->translateKey = $prototypeOption->translateKey;
            $option->checked = $prototypeOption->checked;
            $option->setPosition($prototypeOption->getPosition());

            $this->addOption($option);
        }

        parent::copyFromPrototype($prototype);
    }
}
