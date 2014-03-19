<?php

namespace EcampMaterial\Entity;

use EcampLib\Entity\BaseEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="p_material_dictionary")
 */
class MaterialDictionary extends BaseEntity
{
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $text;

    public function __construct()
    {
        parent::__construct();

    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }

}
