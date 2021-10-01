<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\CategoryRepository;
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
 *
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_fully_authenticated()'],
        'post' => [
            'denormalization_context' => ['groups' => ['write', 'create']],
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
            'security_post_denormalize' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
            'security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)',
        ],
        'patch' => [
            'denormalization_context' => ['groups' => ['write', 'update']],
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
            'security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
        ],
        'delete' => ['security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[ApiFilter(SearchFilter::class, properties: ['camp'])]
class Category extends AbstractContentNodeOwner implements BelongsToCampInterface {
    public const ITEM_NORMALIZATION_CONTEXT = [
        'groups' => ['read', 'Category:PreferredContentTypes'],
        'swagger_definition_name' => 'read',
    ];

    /**
     * The camp to which this category belongs. May not be changed once the category is created.
     *
     * @ORM\ManyToOne(targetEntity="Camp", inversedBy="categories")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[ApiProperty(example: '/camps/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    public ?Camp $camp = null;

    /**
     * The content types that are most likely to be useful for planning programme of this category.
     *
     * @ORM\ManyToMany(targetEntity="ContentType")
     * @ORM\JoinTable(name="category_contenttype",
     *     joinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="contenttype_id", referencedColumnName="id")}
     * )
     */
    #[ApiProperty(example: '["/content_types/1a2b3c4d"]')]
    #[Groups(['read', 'write'])]
    public Collection $preferredContentTypes;

    /**
     * All the programme that is planned in this category.
     *
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="category", orphanRemoval=true)
     */
    #[ApiProperty(readable: false, writable: false)]
    public Collection $activities;

    /**
     * The id of the category that was used as a template for creating this category. Internal for now, is
     * not published through the API.
     *
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    #[ApiProperty(readable: false, writable: false)]
    public ?string $categoryPrototypeId = null;

    /**
     * An abbreviated name of the category, for display in tight spaces, often together with the day and
     * schedule entry number, e.g. LS 3.a, where LS is the category's short name.
     *
     * @ORM\Column(type="text", nullable=false)
     */
    #[ApiProperty(example: 'LS')]
    #[Groups(['read', 'write'])]
    public ?string $short = null;

    /**
     * The full name of the category.
     *
     * @ORM\Column(type="text", nullable=false)
     */
    #[ApiProperty(example: 'Lagersport')]
    #[Groups(['read', 'write'])]
    public ?string $name = null;

    /**
     * The color of the activities in this category, as a hex color string.
     *
     * @ORM\Column(type="string", length=8, nullable=false)
     */
    #[Assert\Regex(pattern: '/^#[0-9a-zA-Z]{6}$/')]
    #[ApiProperty(example: '#4CAF50')]
    #[Groups(['read', 'write'])]
    public ?string $color = null;

    /**
     * Specifies whether the schedule entries of the activities in this category should be numbered
     * using arabic numbers, roman numerals or letters.
     *
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    #[Assert\Choice(choices: ['a', 'A', 'i', 'I', '1'])]
    #[ApiProperty(default: '1', example: '1')]
    #[Groups(['read', 'write'])]
    public string $numberingStyle = '1';

    public function __construct() {
        $this->preferredContentTypes = new ArrayCollection();
        $this->activities = new ArrayCollection();
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

    #[ApiProperty(writable: false)]
    public function setRootContentNode(?ContentNode $rootContentNode) {
        // Overridden to add annotations
        parent::setRootContentNode($rootContentNode);
    }

    #[Assert\DisableAutoMapping]
    #[Groups(['read'])]
    public function getRootContentNode(): ?ContentNode {
        // Getter is here to add annotations to parent class property
        return $this->rootContentNode;
    }

    /**
     * Overridden in order to add annotations.
     *
     * {@inheritdoc}
     *
     * @return ContentNode[]
     */
    #[Groups(['read'])]
    public function getContentNodes(): array {
        return parent::getContentNodes();
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
                return $num;
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
            'M' => 1000,  'CM' => 900,  'D' => 500,   'CD' => 400,
            'C' => 100,   'XC' => 90,   'L' => 50,    'XL' => 40,
            'X' => 10,    'IX' => 9,    'V' => 5,     'IV' => 4,
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
