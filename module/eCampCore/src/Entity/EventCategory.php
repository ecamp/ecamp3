<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * EventCategory
 * @ORM\Entity()
 * @ORM\Table(name="event_categories")
 */
class EventCategory extends BaseEntity
{
    public function __construct() {
        parent::__construct();

    }

    /**
     * @var Camp
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false)
     */
    private $camp;

    /**
     * @var EventType
     * @ORM\ManyToOne(targetEntity="EventType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eventType;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $short;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=8, nullable=false)
     */
    private $color;

    /**
     * @var string
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $numberingStyle;


    /**
     * @return Camp
     */
    public function getCamp(): Camp {
        return $this->camp;
    }

    public function setCamp(Camp $camp): void {
        $this->camp = $camp;
    }


    /**
     * @return EventType
     */
    public function getEventType(): EventType {
        return $this->eventType;
    }

    public function setEventType(EventType $eventType): void {
        $this->eventType = $eventType;

        if ($this->getColor() == null) {
            $this->setColor($eventType->getDefaultColor());
        }
        if ($this->getNumberingStyle() == null) {
            $this->setNumberingStyle($eventType->getDefaultNumberingStyle());
        }
    }


    /**
     * @return string
     */
    public function getShort(): string {
        return $this->short;
    }

    public function setShort(string $short): void {
        $this->short = $short;
    }


    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }


    /**
     * @return string
     */
    public function getColor(): string {
        return $this->color;
    }

    public function setColor(string $color): void {
        $this->color = $color;
    }


    /**
     * @return string
     */
    public function getNumberingStyle(): string {
        return $this->numberingStyle;
    }

    public function setNumberingStyle(string $numberingStyle): void {
        $this->numberingStyle = $numberingStyle;
    }

    
    /**
     * @param int $num
     * @return string
     */
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

    private function getAlphaNum($num) {
        $num--;
        $alphaNum = '';
        if ($num >= 26) {
            $alphaNum .= $this->getAlphaNum(floor($num / 26));
        }
        $alphaNum .= chr(97 + ($num % 26));

        return $alphaNum;
    }

    private function getRomanNum($num) {
        $table = [
            'M'=>1000,  'CM'=>900,  'D'=>500,   'CD'=>400,
            'C'=>100,   'XC'=>90,   'L'=>50,    'XL'=>40,
            'X'=>10,    'IX'=>9,    'V'=>5,     'IV'=>4,
            'I'=>1
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