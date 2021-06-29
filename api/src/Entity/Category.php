<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category.
 *
 * @ORM\Entity
 */
#[ApiResource]
class Category extends AbstractContentNodeOwner implements BelongsToCampInterface {
    /**
     * @ORM\ManyToOne(targetEntity="Camp", inversedBy="categories")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    public ?Camp $camp = null;

    /**
     * @ORM\ManyToMany(targetEntity="ContentType")
     * @ORM\JoinTable(name="category_contenttype",
     *     joinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="contenttype_id", referencedColumnName="id")}
     * )
     *
     * @var ContentType[]
     */
    public Collection $preferredContentTypes;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    public ?string $categoryPrototypeId = null;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public ?string $short = null;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public ?string $name = null;

    /**
     * @ORM\Column(type="string", length=8, nullable=false)
     */
    public ?string $color = null;

    /**
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    public ?string $numberingStyle = null;

    public function __construct() {
        $this->preferredContentTypes = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    public function getPreferredContentTypes(): array {
        return $this->preferredContentTypes->getValues();
    }

    public function addPreferredContentType(ContentType $contentType): void {
        $this->preferredContentTypes->add($contentType);
    }

    public function removePreferredContentType(ContentType $contentType): void {
        $this->preferredContentTypes->removeElement($contentType);
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
