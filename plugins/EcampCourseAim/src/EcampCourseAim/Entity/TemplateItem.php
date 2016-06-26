<?php

namespace EcampCourseAim\Entity;

use EcampCore\Entity\EventPlugin;

use EcampLib\Entity\BaseEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="p_aim_template_item")
 */
class TemplateItem extends BaseEntity
{

    /**
     * @var TemplateItem
     * @ORM\ManyToOne(targetEntity="TemplateItem", inversedBy="children")
     */
    private $parent;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="TemplateItem", mappedBy="parent")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="items", cascade={"persist","remove"})
     */
    private $list;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $text;

    public function __construct(Template $list, TemplateItem $parent = null)
    {
        parent::__construct();
        $this->list = $list;
        $this->parent = $parent;
        $this->children = new ArrayCollection();
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getList()
    {
        return $this->list;
    }

}
