<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Util\ClassInfoTrait;
use App\InputFilter;
use App\Repository\CategoryRepository;
use App\State\CategoryCreateProcessor;
use App\State\CategoryRemoveProcessor;
use App\Util\EntityMap;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A type of programme, such as sports activities or meal times, is called a category. A category
 * determines color and numbering scheme of the associated activities, and is used for marking
 * "similar" activities. A category may contain some skeleton programme which is used as a blueprint
 * when creating a new activity in the category.
 */
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: self::ITEM_NORMALIZATION_CONTEXT,
            security: 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)'
        ),
        new Patch(
            denormalizationContext: ['groups' => ['write', 'update']],
            normalizationContext: self::ITEM_NORMALIZATION_CONTEXT,
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'
        ),
        new Delete(
            processor: CategoryRemoveProcessor::class,
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'
        ),
        new GetCollection(
            security: 'is_authenticated()'
        ),
        new Post(
            processor: CategoryCreateProcessor::class,
            denormalizationContext: ['groups' => ['write', 'create']],
            normalizationContext: self::ITEM_NORMALIZATION_CONTEXT,
            securityPostDenormalize: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
    order: ['camp.id', 'short'],
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['camp'])]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category extends BaseEntity implements BelongsToCampInterface, CopyFromPrototypeInterface {
    use ClassInfoTrait;
    use HasRootContentNodeTrait;

    public const ITEM_NORMALIZATION_CONTEXT = [
        'groups' => [
            'read',
            'Category:PreferredContentTypes',
            'Category:ContentNodes',
        ],
        'swagger_definition_name' => 'read',
    ];

    /**
     * The camp to which this category belongs. May not be changed once the category is created.
     */
    #[ApiProperty(example: '/camps/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    #[ORM\ManyToOne(targetEntity: Camp::class, inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Camp $camp = null;

    /**
     * The content types that are most likely to be useful for planning programme of this category.
     */
    #[ApiProperty(example: '["/content_types/1a2b3c4d"]')]
    #[Groups(['read', 'write'])]
    #[ORM\ManyToMany(targetEntity: ContentType::class, inversedBy: 'categories')]
    #[ORM\JoinTable(name: 'category_contenttype')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'contenttype_id', referencedColumnName: 'id')]
    #[ORM\OrderBy(['name' => 'ASC'])]
    public Collection $preferredContentTypes;

    /**
     * All the programme that is planned in this category.
     */
    #[Assert\Count(
        exactly: 0,
        exactMessage: 'It\'s not possible to delete a category as long as it has an activity linked to it.',
        groups: ['delete']
    )]
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\OneToMany(targetEntity: Activity::class, mappedBy: 'category', orphanRemoval: true)]
    public Collection $activities;

    /**
     * The id of the category that was used as a template for creating this category. Internal for now, is
     * not published through the API.
     */
    #[ApiProperty(readable: false, writable: false)]
    #[ORM\Column(type: 'string', length: 16, nullable: true)]
    public ?string $categoryPrototypeId = null;

    /**
     * An abbreviated name of the category, for display in tight spaces, often together with the day and
     * schedule entry number, e.g. LS 3.a, where LS is the category's short name.
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanText]
    #[Assert\NotBlank]
    #[Assert\Length(max: 16)]
    #[ApiProperty(example: 'LS')]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text', nullable: false)]
    public ?string $short = null;

    /**
     * The full name of the category.
     */
    #[InputFilter\Trim]
    #[InputFilter\CleanText]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[ApiProperty(example: 'Lagersport')]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text', nullable: false)]
    public ?string $name = null;

    /**
     * The color of the activities in this category, as a hex color string.
     */
    #[Assert\Regex(pattern: '/^#[0-9a-zA-Z]{6}$/')]
    #[ApiProperty(example: '#4CAF50')]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'string', length: 8, nullable: false)]
    public ?string $color = null;

    /**
     * Specifies whether the schedule entries of the activities in this category should be numbered
     * using arabic numbers, roman numerals or letters.
     */
    #[Assert\Choice(choices: ['a', 'A', 'i', 'I', '1'])]
    #[ApiProperty(example: '1')]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'string', length: 1, nullable: false)]
    public string $numberingStyle = '1';

    public function __construct() {
        parent::__construct();
        $this->preferredContentTypes = new ArrayCollection();
        $this->activities = new ArrayCollection();
    }

    /**
     * @return ContentNode[]
     */
    #[ApiProperty(readableLink: true)]
    #[SerializedName('contentNodes')]
    #[Groups(['Category:ContentNodes'])]
    public function getEmbeddedContentNodes(): array {
        return $this->getContentNodes();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    /**
     * @return ContentType[]
     */
    #[ApiProperty(readableLink: true)]
    #[SerializedName('preferredContentTypes')]
    #[Groups('Category:PreferredContentTypes')]
    public function getEmbeddedPreferredContentTypes(): array {
        return $this->preferredContentTypes->getValues();
    }

    /**
     * @return ContentType[]
     */
    public function getPreferredContentTypes(): array {
        return $this->preferredContentTypes->getValues();
    }

    public function addPreferredContentType(ContentType $contentType): void {
        $this->preferredContentTypes->add($contentType);
    }

    public function removePreferredContentType(ContentType $contentType): void {
        $this->preferredContentTypes->removeElement($contentType);
    }

    /**
     * @return Activity[]
     */
    public function getActivities(): array {
        return $this->activities->getValues();
    }

    public function addActivity(Activity $activity): void {
        $this->activities->add($activity);
    }

    public function removeActivity(Activity $activity): void {
        $this->activities->removeElement($activity);
    }

    public function getStyledNumber(int $num): string {
        switch ($this->numberingStyle) {
            case 'a':
                return strtolower($this->getAlphaNum($num));

            case 'A':
                return strtoupper($this->getAlphaNum($num));

            case 'i':
                return strtolower($this->getRomanNum($num));

            case 'I':
                return strtoupper($this->getRomanNum($num));

            default:
                return strval($num);
        }
    }

    /**
     * @param Category  $prototype
     * @param EntityMap $entityMap
     */
    public function copyFromPrototype($prototype, $entityMap): void {
        $entityMap->add($prototype, $this);

        $this->categoryPrototypeId = $prototype->getId();
        $this->short = $prototype->short;
        $this->name = $prototype->name;
        $this->color = $prototype->color;
        $this->numberingStyle = $prototype->numberingStyle;

        // copy preferredContentTypes
        foreach ($prototype->getPreferredContentTypes() as $contentType) {
            $this->addPreferredContentType($contentType);
        }

        // copy rootContentNode
        $rootContentNodePrototype = $prototype->getRootContentNode();
        if (null != $rootContentNodePrototype) {
            $rootContentNodeClass = $this->getObjectClass($rootContentNodePrototype);
            $rootContentNode = new $rootContentNodeClass();

            $this->setRootContentNode($rootContentNode);
            $rootContentNode->copyFromPrototype($rootContentNodePrototype, $entityMap);
        }
    }

    private function getAlphaNum($num): string {
        --$num;
        $alphaNum = '';
        if ($num >= 26) {
            $alphaNum .= $this->getAlphaNum(floor($num / 26));
        }
        $alphaNum .= chr(97 + ($num % 26));

        return $alphaNum;
    }

    private function getRomanNum($num): string {
        $table = [
            'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
            'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
            'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4,
            'I' => 1,
        ];
        $romanNum = '';
        while ($num > 0) {
            foreach ($table as $rom => $arb) {
                if ($num >= $arb) {
                    $num -= $arb;
                    $romanNum .= $rom;

                    break;
                }
            }
        }

        return $romanNum;
    }
}
