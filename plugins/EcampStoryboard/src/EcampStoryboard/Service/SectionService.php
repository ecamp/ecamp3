<?php

namespace EcampStoryboard\Service;


use EcampCore\Acl\DefaultAcl;
use EcampCore\Entity\PluginInstance;
use EcampCore\Service\ServiceBase;
use EcampCore\Service\Params\Params;

use EcampStoryboard\Entity\Section;
use EcampStoryboard\Repository\Provider\SectionRepositoryProvider;

class SectionService 
	extends ServiceBase
	implements SectionRepositoryProvider
{
	
	public function _setupAcl(){
		$this->getAcl()->allow(DefaultAcl::CAMP_MEMBER, $this, 'create');
		$this->getAcl()->allow(DefaultAcl::CAMP_MEMBER, $this, 'delete');
		$this->getAcl()->allow(DefaultAcl::CAMP_MEMBER, $this, 'moveUp');
		$this->getAcl()->allow(DefaultAcl::CAMP_MEMBER, $this, 'moveDown');
		
// 		$this->getAcl()->allow(DefaultAcl::GUEST, $this, 'create');
		$this->getAcl()->allow(DefaultAcl::GUEST, $this, 'delete');
// 		$this->getAcl()->allow(DefaultAcl::GUEST, $this, 'moveUp');
// 		$this->getAcl()->allow(DefaultAcl::GUEST, $this, 'moveDown');
	}
	
	
	public function create(
		PluginInstance $pluginInstance,
		Params $params
	){
		$section = new Section($pluginInstance);
		
		if($params->hasElement('duration')){
			$section->setDurationInMinutes($params->getValue('duration'));
		}
		
		if($params->hasElement('text')){
			$section->setText($params->getValue('text'));
		}
		
		if($params->hasElement('info')){
			$section->setInfo($params->getValue('info'));
		}
		
		$position = $this->ecampStoryboard_SectionRepo()->getMaxPosition($pluginInstance) + 1;
		$section->setPosition($position);
		
		$this->persist($section);
		return $section;
	}
	
	
	public function update(
		Section $section,
		Params $params
	){
		if($params->hasElement('duration')){
			$section->setDurationInMinutes($params->getValue('duration'));
		}
		
		if($params->hasElement('text')){
			$section->setText($params->getValue('text'));
		}
		
		if($params->hasElement('info')){
			$section->setInfo($params->getValue('info'));
		}
	}
	
	
	public function delete(Section $section){
		$this->getEM()->remove($section);
	}
	
	
	public function moveUp(Section $section){
		
		$prevSection = $this->ecampStoryboard_SectionRepo()->findPrevSection($section);
		
		if($prevSection == null){
			$this->addValidationMessage("First Section can not be moved up");
		}else{
			$sectionPos = $section->getPosition();
			
			$section->setPosition($prevSection->getPosition());
			$prevSection->setPosition($sectionPos);
		}
	}
	
	public function moveDown(Section $section){
		$nextSection = $this->ecampStoryboard_SectionRepo()->findNextSection($section);
		
		if($nextSection == null){
			$this->addValidationMessage("Last Section can not be moved down");
		}else{
			$sectionPos = $section->getPosition();
			
			$section->setPosition($nextSection->getPosition());
			$nextSection->setPosition($sectionPos);
		}
	}
	
}
