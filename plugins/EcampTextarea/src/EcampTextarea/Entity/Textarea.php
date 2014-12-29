<?php

namespace EcampTextarea\Entity;

use Doctrine\ORM\Mapping as ORM;
use EcampCore\Entity\EventPlugin;
use EcampLib\Entity\BaseEntity;


/**
 * @ORM\Entity(repositoryClass="EcampTextarea\Repository\TextareaRepository")
 * @ORM\Table(name="p_textarea_textarea")
 */
class Textarea extends BaseEntity {

    /**
     * @ORM\OneToOne(targetEntity="EcampCore\Entity\EventPlugin")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $eventPlugin;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;


    /**
     * @param EventPlugin $eventPlugin
     */
    public function __construct(EventPlugin $eventPlugin)
    {
        parent::__construct();
        $this->eventPlugin = $eventPlugin;
    }


    /**
     * @return \EcampCore\Entity\EventPlugin
     */
    public function getEventPlugin()
    {
        return $this->eventPlugin;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

}