<?php

namespace EcampStoryboard\Entity;

use EcampLib\Entity\BaseEntity;
use EcampCore\Entity\PluginInstance;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="EcampStoryboard\Repository\SectionRepository")
 * @ORM\Table(name="p_storyboard_section")
 */
class Section extends BaseEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="EcampCore\Entity\PluginInstance")
     */
    private $pluginInstance;

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

    public function __construct(PluginInstance $pluginInstance)
    {
        parent::__construct();
        $this->pluginInstance = $pluginInstance;
    }

    /**
     * @return EcampCore\Entity\PluginInstance
     */
    public function getPluginInstance()
    {
        return $this->pluginInstance;
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
