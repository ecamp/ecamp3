<?php
return array(

	'factories' => array(
		'ecampstoryboard.repo.section' => new EcampLib\Repository\RepositoryFactory('EcampStoryboard\Entity\Section'),
	),
	
	'initializers' => array(
		function($instance, $sm){
			
			if(! is_object($instance)){
				return;
			}
			
			foreach(class_uses($instance) as $trait){
				switch($trait){
					
					case 'EcampStoryboard\RepositoryTraits\SectionTrait':
						$instance->setSectionRepository($sm->get('ecampstoryboard.repo.section'));
						break;

					default:
						break;
				}
			}
		}
	)
);