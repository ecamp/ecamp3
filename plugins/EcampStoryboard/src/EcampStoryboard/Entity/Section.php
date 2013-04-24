<?php

namespace EcampStoryboard\Entity;

use Doctrine\ORM\Mapping as ORM;

use EcampCore\Entity\BaseEntity;


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
	
	
	
	
}