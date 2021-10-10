<?php

namespace App\Entity\ContentNode;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\ContentNode;
use App\Validator\ColumnLayout\AssertColumWidthsSumTo12;
use App\Validator\ColumnLayout\AssertJsonSchema;
use App\Validator\ColumnLayout\AssertNoOrphanChildren;
use App\Validator\ColumnLayout\ColumnLayoutGroupSequence;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_node_columnlayout")
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
            'validation_groups' => ColumnLayoutGroupSequence::class,
        ],
    ],
    itemOperations: [
        'get' => ['security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'],
        'patch' => [
            'denormalization_context' => ['groups' => ['write', 'update']],
            'security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            'validation_groups' => ColumnLayoutGroupSequence::class,
        ],
        'delete' => ['security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
class ColumnLayout extends ContentNode {
    /**
     * JSON configuration for columns.
     *
     * @ORM\Column(type="json", nullable=true)
     */
    #[ApiProperty(example: "[['slot' => '1', 'width' => 12]]")]
    #[Groups(['read', 'write'])]
    private ?array $columns = null;

    #[AssertJsonSchema(groups: ['columns_schema'])]
    #[AssertColumWidthsSumTo12]
    #[AssertNoOrphanChildren]
    public function getColumns(): ?array {
        if (null !== $this->prototype && null === $this->columns) {
            return $this->prototype->columns;
        }

        return $this->columns;
    }

    public function setColumns(?array $columns) {
        $this->columns = $columns;
    }

    public function copyFromPrototype(ColumnLayout $prototype) {
        if (!isset($this->columns)) {
            $this->columns = $prototype->getColumns();
        }
    }
}
