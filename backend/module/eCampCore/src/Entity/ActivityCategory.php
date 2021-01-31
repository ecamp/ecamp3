<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * ActivityCategory.
 *
 * @ORM\Entity
 */
class ActivityCategory extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\OneToMany(targetEntity="ContentTypeConfig", mappedBy="activityCategory", orphanRemoval=true)
     */
    protected Collection $contentTypeConfigs;

    /**
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Camp $camp = null;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private ?string $activityCategoryTemplateId = null;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private ?string $short = null;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=8, nullable=false)
     */
    private ?string $color = null;

    /**
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private ?string $numberingStyle = null;

    public function __construct() {
        parent::__construct();

        $this->contentTypeConfigs = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    /**
     * @internal Do not set the {@link Camp} directly on the ActivityCategory. Instead use {@see Camp::addActivityCategory()}
     */
    public function setCamp(?Camp $camp) {
        $this->camp = $camp;
    }

    public function getContentTypeConfigs(): Collection {
        return $this->contentTypeConfigs;
    }

    public function addContentTypeConfig(ContentTypeConfig $contentTypeConfig) {
        $contentTypeConfig->setActivityCategory($this);
        $this->contentTypeConfigs->add($contentTypeConfig);
    }

    public function removeContentTypeConfig(ContentTypeConfig $contentTypeConfig) {
        $contentTypeConfig->setActivityCategory(null);
        $this->contentTypeConfigs->removeElement($contentTypeConfig);
    }

    public function getActivityCategoryTemplateId(): ?string {
        return $this->activityCategoryTemplateId;
    }

    public function setActivityCategoryTemplateId(?string $activityCategoryTemplateId) {
        $this->activityCategoryTemplateId = $activityCategoryTemplateId;
    }

    public function getShort(): ?string {
        return $this->short;
    }

    public function setShort(?string $short) {
        $this->short = $short;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name) {
        $this->name = $name;
    }

    public function getColor(): ?string {
        return $this->color;
    }

    public function setColor(?string $color) {
        $this->color = $color;
    }

    public function getNumberingStyle(): ?string {
        return $this->numberingStyle;
    }

    public function setNumberingStyle(?string $numberingStyle) {
        $this->numberingStyle = $numberingStyle;
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
