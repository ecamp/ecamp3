<?php
return array(

	'factories' => array(
		'ecampcore.repo.camp' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Camp'),
		'ecampcore.repo.camptype' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\CampType'),
		'ecampcore.repo.day' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Day'),
		'ecampcore.repo.event' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Event'),
		'ecampcore.repo.eventcategory' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\EventCategory'),
		'ecampcore.repo.eventinstance' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\EventInstance'),
		'ecampcore.repo.eventprototype' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\EventPrototype'),
		'ecampcore.repo.eventresp' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\EventResp'),
		'ecampcore.repo.eventtemplate' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\EventTemplate'),
		'ecampcore.repo.eventtype' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\EventType'),
		'ecampcore.repo.group' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Group'),
		'ecampcore.repo.grouprequest' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\GroupRequest'),
		'ecampcore.repo.image' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Image'),
		'ecampcore.repo.login' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Login'),
		'ecampcore.repo.medium' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Medium'),
		'ecampcore.repo.period' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Period'),
		'ecampcore.repo.plugin' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Plugin'),
		'ecampcore.repo.plugininstance' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\PluginInstance'),
		'ecampcore.repo.pluginposition' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\PluginPosition'),
		'ecampcore.repo.pluginprototype' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\PluginPrototype'),
		'ecampcore.repo.uid' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\UId'),
		'ecampcore.repo.user' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\User'),
		'ecampcore.repo.usercamp' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\UserCamp'),
		'ecampcore.repo.usergroup' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\UserGroup'),
		'ecampcore.repo.userrelationship' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\UserRelationship'),
	),
	
	'initializers' => array(
		function($instance, $sm){
			
			if(! is_object($instance)){
				return;
			}
			
			foreach(class_uses($instance) as $trait){
				switch($trait){
					
					case 'EcampCore\RepositoryTraits\CampTrait':
						$instance->setCampRepository($sm->get('ecampcore.repo.camp'));
						break;

					case 'EcampCore\RepositoryTraits\CampTypeTrait':
						$instance->setCampTypeRepository($sm->get('ecampcore.repo.camptype'));
						break;

					case 'EcampCore\RepositoryTraits\DayTrait':
						$instance->setDayRepository($sm->get('ecampcore.repo.day'));
						break;

					case 'EcampCore\RepositoryTraits\EventTrait':
						$instance->setEventRepository($sm->get('ecampcore.repo.event'));
						break;

					case 'EcampCore\RepositoryTraits\EventCategoryTrait':
						$instance->setEventCategoryRepository($sm->get('ecampcore.repo.eventcategory'));
						break;

					case 'EcampCore\RepositoryTraits\EventInstanceTrait':
						$instance->setEventInstanceRepository($sm->get('ecampcore.repo.eventinstance'));
						break;

					case 'EcampCore\RepositoryTraits\EventPrototypeTrait':
						$instance->setEventPrototypeRepository($sm->get('ecampcore.repo.eventprototype'));
						break;

					case 'EcampCore\RepositoryTraits\EventRespTrait':
						$instance->setEventRespRepository($sm->get('ecampcore.repo.eventresp'));
						break;

					case 'EcampCore\RepositoryTraits\EventTemplateTrait':
						$instance->setEventTemplateRepository($sm->get('ecampcore.repo.eventtemplate'));
						break;

					case 'EcampCore\RepositoryTraits\EventTypeTrait':
						$instance->setEventTypeRepository($sm->get('ecampcore.repo.eventtype'));
						break;

					case 'EcampCore\RepositoryTraits\GroupTrait':
						$instance->setGroupRepository($sm->get('ecampcore.repo.group'));
						break;

					case 'EcampCore\RepositoryTraits\GroupRequestTrait':
						$instance->setGroupRequestRepository($sm->get('ecampcore.repo.grouprequest'));
						break;

					case 'EcampCore\RepositoryTraits\ImageTrait':
						$instance->setImageRepository($sm->get('ecampcore.repo.image'));
						break;

					case 'EcampCore\RepositoryTraits\LoginTrait':
						$instance->setLoginRepository($sm->get('ecampcore.repo.login'));
						break;

					case 'EcampCore\RepositoryTraits\MediumTrait':
						$instance->setMediumRepository($sm->get('ecampcore.repo.medium'));
						break;

					case 'EcampCore\RepositoryTraits\PeriodTrait':
						$instance->setPeriodRepository($sm->get('ecampcore.repo.period'));
						break;

					case 'EcampCore\RepositoryTraits\PluginTrait':
						$instance->setPluginRepository($sm->get('ecampcore.repo.plugin'));
						break;

					case 'EcampCore\RepositoryTraits\PluginInstanceTrait':
						$instance->setPluginInstanceRepository($sm->get('ecampcore.repo.plugininstance'));
						break;

					case 'EcampCore\RepositoryTraits\PluginPositionTrait':
						$instance->setPluginPositionRepository($sm->get('ecampcore.repo.pluginposition'));
						break;

					case 'EcampCore\RepositoryTraits\PluginPrototypeTrait':
						$instance->setPluginPrototypeRepository($sm->get('ecampcore.repo.pluginprototype'));
						break;

					case 'EcampCore\RepositoryTraits\UIdTrait':
						$instance->setUIdRepository($sm->get('ecampcore.repo.uid'));
						break;

					case 'EcampCore\RepositoryTraits\UserTrait':
						$instance->setUserRepository($sm->get('ecampcore.repo.user'));
						break;

					case 'EcampCore\RepositoryTraits\UserCampTrait':
						$instance->setUserCampRepository($sm->get('ecampcore.repo.usercamp'));
						break;

					case 'EcampCore\RepositoryTraits\UserGroupTrait':
						$instance->setUserGroupRepository($sm->get('ecampcore.repo.usergroup'));
						break;

					case 'EcampCore\RepositoryTraits\UserRelationshipTrait':
						$instance->setUserRelationshipRepository($sm->get('ecampcore.repo.userrelationship'));
						break;

					default:
						break;
				}
			}
		}
	)
);