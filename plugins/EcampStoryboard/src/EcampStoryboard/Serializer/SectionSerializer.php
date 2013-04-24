<?php

namespace EcampStoryboard\Serializer;

use EcampApi\Serializer\BaseSerializer;
use EcampStoryboard\Entity\Section;

class SectionSerializer extends BaseSerializer{
	
	protected function serialize($section){
		return array(
    		'id' 		=> 	$section->getId(),
			'href'		=>	$this->getSectionHref($section), 
		);
	}
	
	public function getReference(Section $section = null){
		if($section == null){
			return null;
		} else {
			return array(
				'id'	=>	$section->getId(),
				'href'	=>	$this->getCampHref($camp)
			);
		}
	}
	
	private function getSectionHref(Section $section){
		return
			$this->router->assemble(
				array(
					'controller' => 'section',
					'action' => 'get',
					'id' => $section->getId(),
					'format' => $this->format
				),
				array(
					'name' => 'plugin-storyboard/default',
				)
			);
	}
}