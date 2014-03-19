<?php

namespace EcampStoryboard\Entity;

use EcampLib\Entity\BaseEntity;

use Doctrine\ORM\Mapping as ORM;
use EcampCore\Entity\EventPlugin;

/**
 * @ORM\Entity(repositoryClass="EcampStoryboard\Repository\SectionRepository")
 * @ORM\Table(name="p_storyboard_section")
 */
class Section extends BaseEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="EcampCore\Entity\EventPlugin")
     */
    private $eventPlugin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $info;

    public function __construct(EventPlugin $eventPlugin)
    {
        parent::__construct();
        $this->eventPlugin = $eventPlugin;
    }

    /**
     * @return EcampCore\Entity\EventPlugin
     */
    public function getEventPlugin()
    {
        return $this->eventPlugin;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setDurationInMinutes($duration)
    {
        $this->duration = $duration;
    }

    public function getDurationInMinutes()
    {
        return $this->duration;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setInfo($info)
    {
        $this->info = $info;
    }

    public function getInfo()
    {
        return $this->info;
    }

}
