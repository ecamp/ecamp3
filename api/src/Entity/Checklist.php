<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\InputFilter;
use App\Repository\ChecklistRepository;
use App\State\ChecklistCreateProcessor;
use App\Util\EntityMap;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A Checklist
 * Tree-Structure with ChecklistItems.
 */
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'
        ),
        new Patch(
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'
        ),
        new Delete(
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            validate: true,
            validationContext: ['groups' => ['delete']]
        ),
        new GetCollection(
            security: 'is_authenticated()'
        ),
        new Post(
            processor: ChecklistCreateProcessor::class,
            denormalizationContext: ['groups' => ['write', 'create']],
            securityPostDenormalize: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'
        ),
        new GetCollection(
            name: 'BelongsToCamp_App\Entity\Checklist_get_collection',
            uriTemplate: self::CAMP_SUBRESOURCE_URI_TEMPLATE,
            uriVariables: [
                'campId' => new Link(
                    fromClass: Camp::class,
                    toProperty: 'camp',
                    security: 'is_granted("CAMP_COLLABORATOR", camp) or is_granted("CAMP_IS_PROTOTYPE", camp)'
                ),
            ],
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
    order: ['camp.id', 'name'],
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['camp'])]
#[ORM\Entity(repositoryClass: ChecklistRepository::class)]
class Checklist extends BaseEntity implements BelongsToCampInterface, CopyFromPrototypeInterface {
    public const CAMP_SUBRESOURCE_URI_TEMPLATE = '/camps/{campId}/checklists.{_format}';

    /**
     * The camp this checklist belongs to.
     */
    #[ApiProperty(example: '/camps/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    #[ORM\ManyToOne(targetEntity: Camp::class, inversedBy: 'checklists')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Camp $camp = null;

    /**
     * Copy contents from this source checklist.
     */
    #[ApiProperty(example: '/checklists/1a2b3c4d')]
    #[Groups(['create'])]
    public ?Checklist $copyChecklistSource;

    /**
     * The human readable name of the checklist.
     */
    #[ApiProperty(example: 'PBS Ausbildungsziele')]
    #[Groups(['read', 'write'])]
    #[InputFilter\Trim]
    #[InputFilter\CleanText]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[ORM\Column(type: 'text')]
    public ?string $name = null;

    public function __construct() {
        parent::__construct();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    /**
     * @param Checklist $prototype
     * @param EntityMap $entityMap
     */
    public function copyFromPrototype($prototype, $entityMap): void {
        $entityMap->add($prototype, $this);

        $this->name = $prototype->name;
    }
}
