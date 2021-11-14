<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\ContentNode;
use App\InputFilter;
use App\Repository\SingleTextRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=SingleTextRepository::class)
 * @ORM\Table(name="content_node_singletext")
 */
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
class SingleText extends ContentNode {
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[InputFilter\CleanHTML]
    #[Groups(['read', 'write'])]
    public ?string $text = null;

    /**
     * @param SingleText $prototype
     */
    public function copyFromPrototype($prototype) {
        if (!isset($this->text)) {
            $this->text = $prototype->text;
        }

        parent::copyFromPrototype($prototype);
    }
}
